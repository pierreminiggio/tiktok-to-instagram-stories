<?php

namespace PierreMiniggio\TiktokToInstagramStories\Repository;

use PierreMiniggio\DatabaseFetcher\DatabaseFetcher;

class StoryToPostRepository
{
    public function __construct(private DatabaseFetcher $fetcher)
    {}

    public function insertStoryIfNeeded(
        string $instagramId,
        int $instagramChannelId,
        int $tiktokVideoId
    ): void
    {
        $postQueryParams = [
            'channel_id' => $instagramChannelId,
            'instagram_id' => $instagramId
        ];
        $findPostIdQuery = [
            $this->fetcher
                ->createQuery('instagram_story')
                ->select('id')
                ->where('channel_id = :channel_id AND instagram_id = :instagram_id')
            ,
            $postQueryParams
        ];
        $queriedIds = $this->fetcher->query(...$findPostIdQuery);
        
        if (! $queriedIds) {
            $this->fetcher->exec(
                $this->fetcher
                    ->createQuery('instagram_story')
                    ->insertInto(
                        'channel_id, instagram_id',
                        ':channel_id, :instagram_id'
                    )
                ,
                $postQueryParams
            );
            $queriedIds = $this->fetcher->query(...$findPostIdQuery);
        }

        $postId = (int) $queriedIds[0]['id'];
        
        $pivotQueryParams = [
            'instagram_id' => $postId,
            'tiktok_id' => $tiktokVideoId
        ];

        $queriedPivotIds = $this->fetcher->query(
            $this->fetcher
                ->createQuery('instagram_story_tiktok_video')
                ->select('id')
                ->where('instagram_id = :instagram_id AND tiktok_id = :tiktok_id')
            ,
            $pivotQueryParams
        );
        
        if (! $queriedPivotIds) {
            $this->fetcher->exec(
                $this->fetcher
                    ->createQuery('instagram_story_tiktok_video')
                    ->insertInto('instagram_id, tiktok_id', ':instagram_id, :tiktok_id')
                ,
                $pivotQueryParams
            );
        }
    }
}
