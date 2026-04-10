<?php

namespace JustBetter\BardLinkSuggestions\Actions;

use JustBetter\BardLinkSuggestions\Contracts\IndexesSitemaps;
use JustBetter\BardLinkSuggestions\Jobs\IndexSitemapJob;
use JustBetter\BardLinkSuggestions\Models\SuggestionSetting;

class IndexSitemaps implements IndexesSitemaps
{
    public function index(SuggestionSetting $setting): void
    {
        foreach ($setting->queryable_fields as $url) {
            IndexSitemapJob::dispatch($setting, $url);
        }
    }

    public static function bind(): void
    {
        app()->singleton(IndexesSitemaps::class, static::class);
    }
}
