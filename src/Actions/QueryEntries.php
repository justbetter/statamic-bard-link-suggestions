<?php

namespace JustBetter\BardLinkSuggestions\Actions;

use Illuminate\Database\Eloquent\Collection;
use JustBetter\BardLinkSuggestions\Contracts\QueriesEntries;
use JustBetter\BardLinkSuggestions\Models\SuggestionSetting;
use Statamic\Eloquent\Entries\EntryQueryBuilder;
use Statamic\Entries\EntryCollection;
use Statamic\Facades\Entry as EntryFacade;
use Statamic\Sites\Site;
use Statamic\Stache\Query\EntryQueryBuilder as StacheEntryQueryBuilder;

class QueryEntries implements QueriesEntries
{
    /**
     * @param  Collection<int, SuggestionSetting>  $settings
     */
    public function query(string $searchQuery, Collection $settings, Site $site): EntryCollection
    {
        /** @var EntryQueryBuilder $query */
        $query = EntryFacade::query();
        $query->where('site', '=', $site->handle())->whereNotNull('slug');

        foreach ($settings as $setting) {
            $collectionHandle = $setting->collection_handle;
            $queryableFields = $setting->queryable_fields;

            $query->where(function (EntryQueryBuilder|StacheEntryQueryBuilder $query) use ($collectionHandle, $queryableFields, $searchQuery) {
                $query->orWhere(function (EntryQueryBuilder|StacheEntryQueryBuilder $query) use ($collectionHandle, $queryableFields, $searchQuery) {
                    $query->where('collection', $collectionHandle);

                    foreach ($queryableFields as $field) {
                        $query->orWhere($field, 'like', '%'.$searchQuery.'%');
                    }
                });
            });
        }

        return $query->get();
    }

    public static function bind(): void
    {
        app()->singleton(QueriesEntries::class, static::class);
    }
}
