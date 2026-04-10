<?php

namespace JustBetter\BardLinkSuggestions\Tests\Actions;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use JustBetter\BardLinkSuggestions\Actions\IndexSitemap;
use JustBetter\BardLinkSuggestions\Models\SitemapUrl;
use JustBetter\BardLinkSuggestions\Models\SuggestionSetting;
use JustBetter\BardLinkSuggestions\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class IndexSitemapTest extends TestCase
{
    #[Test]
    public function it_indexes_sitemap_urls(): void
    {
        Bus::fake();

        $xml = <<<'XML'
            <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
                <url><loc>https://example.com/DAF-truck</loc></url>
                <url><loc>https://example.com/Scania</loc></url>
                <url><loc>https://example.com/Another-DAF-model</loc></url>
            </urlset>
        XML;

        Http::fake([
            'https://example.com/sitemap.xml' => Http::response($xml, 200),
        ])->preventStrayRequests();

        $suggestion = SuggestionSetting::create([
            'collection_handle' => 'sitemap_en',
            'queryable_fields' => ['https://example.com/sitemap.xml'],
        ]);

        $action = app(IndexSitemap::class);

        $action->index($suggestion, 'https://example.com/sitemap.xml');

        $this->assertCount(3, SitemapUrl::all());
    }

    #[Test]
    public function it_does_nothing_if_response_empty(): void
    {
        Bus::fake();

        Http::fake([
            'https://example.com/sitemap.xml' => Http::response(null, 200),
        ])->preventStrayRequests();

        $suggestion = SuggestionSetting::create([
            'collection_handle' => 'sitemap_en',
            'queryable_fields' => ['https://example.com/sitemap.xml'],
        ]);

        $action = app(IndexSitemap::class);

        $action->index($suggestion, 'https://example.com/sitemap.xml');

        $this->assertCount(0, SitemapUrl::all());
    }
}
