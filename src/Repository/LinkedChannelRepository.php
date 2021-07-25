<?php

namespace PierreMiniggio\TiktokToInstagramStories\Repository;

use PierreMiniggio\DatabaseFetcher\DatabaseFetcher;

class LinkedChannelRepository
{
    public function __construct(private DatabaseFetcher $fetcher)
    {}

    public function findAll(): array
    {
        return $this->fetcher->query(
            $this->fetcher->createQuery(
                'instagram_stories_channel_tiktok_account as iscta'
            )->join(
                'instagram_stories_channel as s',
                's.id = iscta.instagram_id'
            )->select(
                'iscta.tiktok_id as t_id',
                's.id as i_id',
                's.action_uploader_account_name as action_uploader_account_name'
            )
        );
    }
}
