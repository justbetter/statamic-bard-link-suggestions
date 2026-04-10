<?php

namespace JustBetter\BardLinkSuggestions\Actions;

use Illuminate\Support\Facades\Http;
use JustBetter\BardLinkSuggestions\Contracts\PrunesSitemap;
use JustBetter\BardLinkSuggestions\Models\SitemapUrl;
use SimpleXMLElement;

class PruneSitemap implements PrunesSitemap
{
    public function prune(string $sitemap): void
    {
        $response = Http::get($sitemap);
        $contents = simplexml_load_string($response->body());

        if (! $contents) {
            return;
        }

        foreach ($contents->url as $url) {
            if ($url->loc instanceof SimpleXMLElement) {
                $url = (string) $url->loc;

                SitemapUrl::where('url', $url)->delete();
            }
        }
    }

    public static function bind(): void
    {
        app()->singleton(PrunesSitemap::class, static::class);
    }
}
