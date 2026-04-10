<?php

namespace JustBetter\BardLinkSuggestions\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use JustBetter\BardLinkSuggestions\Contracts\QueriesEntries;
use JustBetter\BardLinkSuggestions\Contracts\QueriesSitemaps;
use JustBetter\BardLinkSuggestions\Models\SuggestionSetting;
use Statamic\Facades\Site;

class SuggestionsController
{
    public function __invoke(Request $request, QueriesEntries $queryEntries, QueriesSitemaps $querySitemaps): JsonResponse
    {
        $validatedQuery = $request->validate([
            'query' => 'required',
        ]);

        $site = Site::selected();

        $suggestionSettings = SuggestionSetting::all();

        $collectionsSettings = $suggestionSettings->filter(fn (SuggestionSetting $setting) => ! str($setting->collection_handle)->startsWith('sitemap_'));

        $results = $queryEntries->query($validatedQuery['query'], $collectionsSettings, $site);

        $sitemapSettings = $suggestionSettings->filter(function (SuggestionSetting $setting) use ($site) {
            return $setting->collection_handle === 'sitemap_'.$site->handle();
        });

        $matchingUrls = $querySitemaps->query($validatedQuery['query'], $sitemapSettings, $site);
        $results = $results->merge($matchingUrls);

        return response()->json($results);
    }
}
