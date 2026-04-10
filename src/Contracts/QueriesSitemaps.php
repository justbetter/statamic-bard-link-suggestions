<?php

namespace JustBetter\BardLinkSuggestions\Contracts;

use Illuminate\Database\Eloquent\Collection;
use JustBetter\BardLinkSuggestions\Models\SuggestionSetting;
use Statamic\Sites\Site;

interface QueriesSitemaps
{
    /**
     * @param  Collection<int, SuggestionSetting>  $settings
     * @return array<int, string>
     */
    public function query(string $searchQuery, Collection $settings, Site $site): array;
}
