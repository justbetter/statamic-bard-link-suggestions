<?php

namespace JustBetter\BardLinkSuggestions\Actions;

use JustBetter\BardLinkSuggestions\Contracts\DispatchesIndexSitemaps;
use JustBetter\BardLinkSuggestions\Jobs\IndexSitemapsJob;
use JustBetter\BardLinkSuggestions\Models\SuggestionSetting;

class DispatchIndexSitemaps implements DispatchesIndexSitemaps
{
    public function dispatch(): void
    {
        $settings = SuggestionSetting::query()->where('collection_handle', 'like', 'sitemap_%')->get();
        foreach ($settings as $setting) {
            IndexSitemapsJob::dispatch($setting);
        }
    }

    public static function bind(): void
    {
        app()->singleton(DispatchesIndexSitemaps::class, static::class);
    }
}
