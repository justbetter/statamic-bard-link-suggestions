<?php

namespace JustBetter\BardLinkSuggestions\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use JustBetter\BardLinkSuggestions\Models\SuggestionSetting;
use Statamic\Facades\Collection;
use Statamic\Facades\Site;
use Statamic\Fields\Field;

class SuggestionsSettingsController
{
    public function index(): mixed
    {
        $collections = Collection::all();
        $sites = Site::all();

        $collectionFields = [];
        $initialSettings = [];
        foreach ($collections as $collection) {
            $blueprint = $collection->entryBlueprints()->first();
            $fields = $blueprint->fields()->all();
            $collectionFields[$collection->handle()] = $fields->map(fn (Field $field): string => $field->handle());
            $settings = SuggestionSetting::where('collection_handle', $collection->handle())->first();
            $initialSettings[$collection->handle()] = $settings ? $settings->queryable_fields : [];
        }

        $initialSettings['sitemapUrls'] = SuggestionSetting::query()
            ->where('collection_handle', 'like', 'sitemap_%')
            ->get()
            ->flatMap(function (SuggestionSetting $setting): array {
                return array_map(function (string $url) use ($setting): array {
                    return [
                        'url' => $url,
                        'site' => str_replace('sitemap_', '', $setting->collection_handle),
                    ];
                }, $setting->queryable_fields);
            });

        return view('statamic-bard-suggestions::settings', [
            'collections' => $collections,
            'collectionFields' => $collectionFields,
            'initialSettings' => $initialSettings,
            'sites' => $sites,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'queryable_collections' => 'array',
            'queryable_fields' => 'array',
            'sitemap_urls' => 'array',
        ]);

        foreach ($data['queryable_fields'] as $collectionHandle => $fields) {
            $settings = SuggestionSetting::query()->firstOrCreate(['collection_handle' => $collectionHandle], [
                'queryable_fields' => [],
            ]);

            $settings->update([
                'queryable_fields' => $fields,
            ]);
        }

        foreach ($data['sitemap_urls'] as $sitemap) {
            $settings = SuggestionSetting::query()->firstOrCreate(['collection_handle' => 'sitemap_'.$sitemap['site']], [
                'queryable_fields' => [],
            ]);

            $currentUrls = $settings->queryable_fields;
            $updatedUrls = array_unique(array_merge($currentUrls, [$sitemap['url']]));

            $settings->update([
                'queryable_fields' => $updatedUrls,
            ]);
        }

        $requestUrls = array_column($data['sitemap_urls'], 'url');
        SuggestionSetting::query()->where('collection_handle', 'like', 'sitemap_%')->get()->each(function (SuggestionSetting $setting) use ($requestUrls) {
            $setting->update([
                'queryable_fields' => array_intersect($setting->queryable_fields, $requestUrls),
            ]);
        });

        return response()->json(['success' => true]);
    }
}
