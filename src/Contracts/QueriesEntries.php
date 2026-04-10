<?php

namespace JustBetter\BardLinkSuggestions\Contracts;

use Illuminate\Database\Eloquent\Collection;
use JustBetter\BardLinkSuggestions\Models\SuggestionSetting;
use Statamic\Entries\EntryCollection;
use Statamic\Sites\Site;

interface QueriesEntries
{
    /**
     * @param  Collection<int, SuggestionSetting>  $settings
     */
    public function query(string $query, Collection $settings, Site $site): EntryCollection;
}
