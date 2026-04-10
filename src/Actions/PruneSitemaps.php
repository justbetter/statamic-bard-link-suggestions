<?php

namespace JustBetter\BardLinkSuggestions\Actions;

use JustBetter\BardLinkSuggestions\Contracts\PrunesSitemaps;
use JustBetter\BardLinkSuggestions\Jobs\PruneSitemapJob;

class PruneSitemaps implements PrunesSitemaps
{
    public function prune(array $sitemaps): void
    {
        foreach ($sitemaps as $sitemap) {
            PruneSitemapJob::dispatch($sitemap);
        }
    }

    public static function bind(): void
    {
        app()->singleton(PrunesSitemaps::class, static::class);
    }
}
