<?php

namespace PierreMiniggio\TiktokToInstagramStories;

use Exception;
use PierreMiniggio\DatabaseFetcher\DatabaseFetcher;
use PierreMiniggio\InstagramStoryPoster\InstagramStoryPoster;
use PierreMiniggio\MultiSourcesTiktokDownloader\MultiSourcesTiktokDownloader;
use PierreMiniggio\TiktokToInstagramStories\Connection\DatabaseConnectionFactory;
use PierreMiniggio\TiktokToInstagramStories\Repository\LinkedChannelRepository;
use PierreMiniggio\TiktokToInstagramStories\Repository\NonUploadedVideoRepository;
use PierreMiniggio\TiktokToInstagramStories\Repository\StoryToPostRepository;
use PierreMiniggio\VideoRenderForTiktokVideoChecker\VideoRenderForTiktokVideoChecker;

class App
{

    public function run(): int
    {

        $code = 0;

        $config = require(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config.php');

        if (empty($config['db'])) {
            echo 'No DB config';

            return $code;
        }

        $cacheDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cache';
        if (! file_exists($cacheDir)) {
            mkdir($cacheDir);
        }
        
        $cacheUrl = $config['cache_url'];
        $downloader = MultiSourcesTiktokDownloader::buildSelf();
        
        $spinnerApiConfig = $config['spinner_api'];
        $spinnerApiUrl = $spinnerApiConfig !== null ? $spinnerApiConfig['url'] : null;
        $spinnerApiToken = $spinnerApiConfig !== null ? $spinnerApiConfig['token'] : null;

        $databaseFetcher = new DatabaseFetcher((new DatabaseConnectionFactory())->makeFromConfig($config['db']));
        $channelRepository = new LinkedChannelRepository($databaseFetcher);
        $nonUploadedVideoRepository = new NonUploadedVideoRepository($databaseFetcher);
        $videoToPostRepository = new StoryToPostRepository($databaseFetcher);

        $uploaderProjects = $config['uploaderProjects'];
        $uploaderProject = $uploaderProjects[array_rand($uploaderProjects)];

        $storyUploader = new InstagramStoryPoster();
        $runnerAndDownloader = $storyUploader->getRunnerAndDownloader();
        $runnerAndDownloader->sleepTimeBetweenRunCreationChecks = 30;
        $runnerAndDownloader->numberOfRunCreationChecksBeforeAssumingItsNotCreated = 20;

        $linkedChannels = $channelRepository->findAll();

        if (! $linkedChannels) {
            echo 'No linked channels';

            return $code;
        }

        foreach ($linkedChannels as $linkedChannel) {

            $instagramChannelId = $linkedChannel['i_id'];
            echo PHP_EOL . PHP_EOL . 'Checking channel ' . $instagramChannelId . '...';

            $videosToPost = $nonUploadedVideoRepository->findByInstagramStoriesAndTiktokChannelIds(
                $instagramChannelId,
                $linkedChannel['t_id']
            );
            echo PHP_EOL . count($videosToPost) . ' videos to post :' . PHP_EOL;

            foreach ($videosToPost as $videoToPost) {
                $legend = $videoToPost['legend'];
                echo PHP_EOL . 'Posting ' . $legend . ' ...';

                $videoToPostId = $videoToPost['id'];
                $videoFile = $cacheDir . DIRECTORY_SEPARATOR . $videoToPostId . '.mp4';
                $videoToPostUrl = $videoToPost['url'];

                $videoUrl = $spinnerApiUrl === null || $spinnerApiToken === null
                    ? null
                    : (new VideoRenderForTiktokVideoChecker($spinnerApiUrl, $spinnerApiToken))
                        ->getRenderedVideoUrl($videoToPostUrl)
                ;

                if ($videoUrl === null) {
                    try {
                        $this->downloadVideoFileIfNeeded($downloader, $videoToPostUrl, $videoFile);
                    } catch (Exception) {
                        break;
                    }

                    $explodedVideoFilePath = explode(DIRECTORY_SEPARATOR, $videoFile);
                    $fileName = $explodedVideoFilePath[count($explodedVideoFilePath) - 1];
                    $videoUrl = $cacheUrl . '/' . $fileName;
                }

                $storyId = $storyUploader->upload(
                    $uploaderProject['token'],
                    $uploaderProject['account'],
                    $uploaderProject['project'],
                    30,
                    0,
                    [
                        'account' => $linkedChannel['action_uploader_account_name'],
                        'video_url' => $videoUrl,
                        'proxy' => $config['proxy']
                    ]
                );

                $videoToPostRepository->insertStoryIfNeeded(
                    $storyId,
                    $instagramChannelId,
                    $videoToPostId
                );
                
                if (file_exists($videoFile)) {
                    unlink($videoFile);
                }

                echo PHP_EOL . $legend . ' posted !';
            }

            echo PHP_EOL . PHP_EOL . 'Done for channel ' . $instagramChannelId . ' !';
        }

        return $code;
    }
    
    /**
     * @throws Exception
     */
    protected function downloadVideoFileIfNeeded(
        MultiSourcesTiktokDownloader $downloader,
        string $videoToPostUrl,
        string $videoFile
    ): void
    {
        if (file_exists($videoFile)) {
            return;
        }

        $temporaryVideoFile = $downloader->download($videoToPostUrl);
        rename($temporaryVideoFile, $videoFile);

        if (! file_exists($videoFile)) {
            throw new Exception('Missing video file');
        }
    }
}
