<?php

namespace JustBetter\BardLinkSuggestions\Actions;

use Illuminate\Database\Eloquent\Collection;
use JustBetter\BardLinkSuggestions\Contracts\QueriesSitemaps;
use JustBetter\BardLinkSuggestions\Models\SitemapUrl;
use JustBetter\BardLinkSuggestions\Models\SuggestionSetting;
use Statamic\Sites\Site;

class QuerySitemaps implements QueriesSitemaps
{
    /**
     * @param  Collection<int, SuggestionSetting>  $settings
     * @return array<int, string>
     */
    public function query(string $searchQuery, Collection $settings, Site $site): array
    {
        return SitemapUrl::query()
            ->whereIn('suggestion_setting_id', $settings->pluck('id'))
            ->where('url', 'LIKE', "%{$searchQuery}%")
            ->pluck('url')
            ->toArray();
    }

    public static function bind(): void
    {
        app()->singleton(QueriesSitemaps::class, static::class);
    }
}
