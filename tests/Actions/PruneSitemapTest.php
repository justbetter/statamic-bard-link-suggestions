<?php

namespace JustBetter\BardLinkSuggestions\Tests\Actions;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use JustBetter\BardLinkSuggestions\Actions\PruneSitemap;
use JustBetter\BardLinkSuggestions\Models\SitemapUrl;
use JustBetter\BardLinkSuggestions\Models\SuggestionSetting;
use JustBetter\BardLinkSuggestions\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PruneSitemapTest extends TestCase
{
    #[Test]
    public function it_does_nothing_if_response_empty(): void
    {
        Bus::fake();

        Http::fake([
            'https://example.com/sitemap.xml' => Http::response(null, 200),
        ])->preventStrayRequests();

        $setting = SuggestionSetting::create([
            'collection_handle' => 'sitemap_en',
            'queryable_fields' => ['https://example.com/sitemap.xml'],
        ]);

        $setting->sitemapUrls()->create([
            'url' => 'https://example.com/test',
        ]);

        $action = app(PruneSitemap::class);

        $action->prune('https://example.com/sitemap.xml');

        $this->assertSame(1, SitemapUrl::count());
    }
}
