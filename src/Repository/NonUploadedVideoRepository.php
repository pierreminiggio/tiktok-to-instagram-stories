<?php

namespace PierreMiniggio\TiktokToInstagramStories\Repository;

use PierreMiniggio\DatabaseFetcher\DatabaseFetcher;

class NonUploadedVideoRepository
{
    public function __construct(private DatabaseFetcher $fetcher)
    {}

    public function findByInstagramStoriesAndTiktokChannelIds(
        int $instagramChannelId,
        int $tiktokChannelId
    ): array
    {
        $postedInstagramStoriesIds = $this->fetcher->query(
            $this->fetcher
                ->createQuery('instagram_story_tiktok_video as istv')
                ->join('instagram_story as g', 'g.id = istv.instagram_id')
                ->select('g.id')
                ->where('g.channel_id = :channel_id')
            ,
            ['channel_id' => $instagramChannelId]
        );
        $postedInstagramStoriesIds = array_map(fn ($entry) => (int) $entry['id'], $postedInstagramStoriesIds);

        $query = $this->fetcher
            ->createQuery('tiktok_video as t')
            ->select('t.id, t.legend, t.tiktok_url as url')
            ->where('t.account_id = :channel_id' . (
                $postedInstagramStoriesIds ? ' AND istv.id IS NULL' : ''
            ))
            ->limit(1)
        ;

        if ($postedInstagramStoriesIds) {
            $query->join(
                'instagram_story_tiktok_video as istv',
                't.id = istv.tiktok_id AND istv.instagram_id IN (' . implode(', ', $postedInstagramStoriesIds) . ')'
            );
        }

        return $this->fetcher->query($query, ['channel_id' => $tiktokChannelId]);
    }
}
