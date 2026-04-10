<?php

namespace JustBetter\BardLinkSuggestions\Tests\Models;

use Illuminate\Support\Facades\Bus;
use JustBetter\BardLinkSuggestions\Models\SuggestionSetting;
use JustBetter\BardLinkSuggestions\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SitemapUrlTest extends TestCase
{
    #[Test]
    public function it_can_have_relationships(): void
    {
        Bus::fake();

        $setting = SuggestionSetting::withoutEvents(fn () => SuggestionSetting::create([
            'collection_handle' => 'sitemap_en',
            'queryable_fields' => ['https://example.com/sitemap.xml'],
        ]));

        $sitemapUrl = $setting->sitemapUrls()->create([
            'url' => 'https://example.com/DAF-truck',
        ]);

        $this->assertSame($setting->sitemapUrls()->count(), 1);
        $this->assertSame($sitemapUrl->suggestion_setting_id, $sitemapUrl->setting->id);
    }
}
