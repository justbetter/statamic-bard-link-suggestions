<?php

namespace JustBetter\BardLinkSuggestions\Actions;

use Illuminate\Support\Facades\Http;
use JustBetter\BardLinkSuggestions\Contracts\IndexesSitemap;
use JustBetter\BardLinkSuggestions\Models\SuggestionSetting;
use SimpleXMLElement;

class IndexSitemap implements IndexesSitemap
{
    public function index(SuggestionSetting $setting, string $url): void
    {
        $response = Http::get($url);
        $contents = simplexml_load_string($response->body());

        if (! $contents) {
            return;
        }

        foreach ($contents->url as $url) {
            if ($url->loc instanceof SimpleXMLElement) {
                $url = (string) $url->loc;

                $setting->sitemapUrls()->updateOrCreate(['url' => $url]);
            }
        }
    }

    public static function bind(): void
    {
        app()->singleton(IndexesSitemap::class, static::class);
    }
}
