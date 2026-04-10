<?php

namespace JustBetter\BardLinkSuggestions\Tests\Http\Controllers;

use Illuminate\Support\Facades\Http;
use JustBetter\BardLinkSuggestions\Models\SuggestionSetting;
use JustBetter\BardLinkSuggestions\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Entries\Entry;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry as EntryFacade;

class SuggestionsControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Collection::make('terminology')->save();
        Collection::make('blog')->save();

        /** @var Entry $entry */
        $entry = EntryFacade::make();
        $entry
            ->collection('terminology')
            ->slug('some-term')
            ->data(['title' => 'Some Term'])
            ->save();

        /** @var Entry $entry */
        $entry = EntryFacade::make();
        $entry
            ->collection('terminology')
            ->slug('another-one')
            ->data(['title' => 'Another Term'])
            ->save();

        /** @var Entry $entry */
        $entry = EntryFacade::make();
        $entry
            ->collection('blog')
            ->slug('irrelevant')
            ->data(['title' => 'No Match'])
            ->save();

        SuggestionSetting::create([
            'collection_handle' => 'terminology',
            'queryable_fields' => ['title'],
        ]);

        SuggestionSetting::create([
            'collection_handle' => 'blog',
            'queryable_fields' => ['title'],
        ]);

        $xml = <<<'XML'
                <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
                <url><loc>https://example.com/DAF-truck</loc></url>
                <url><loc>https://example.com/Scania</loc></url>
                <url><loc>https://example.com/Another-DAF-model</loc></url>
            </urlset>
        XML;

        Http::fake([
            'https://example.com/sitemap.xml' => Http::response($xml, 200),
            'https://example.com/sitemap-fail.xml' => Http::response($xml, 404),
        ])->preventStrayRequests();
    }

    #[Test]
    public function it_returns_suggestions_from_entries(): void
    {
        $response = $this->withoutMiddleware()
            ->postJson(route('statamic.cp.justbetter.suggestions', ['query' => 'term']));

        $response->assertStatus(200);

        /** @var array<int, array<string, string>> $data */
        $data = $response->json();
        $data = collect($data);

        $this->assertEqualsCanonicalizing(['Another Term', 'Some Term'], $data->unique('title')->reverse()->pluck('title')->toArray());
    }

    #[Test]
    public function it_returns_suggestions_from_sitemap(): void
    {
        $suggestion = SuggestionSetting::create([
            'collection_handle' => 'sitemap_en',
            'queryable_fields' => ['https://example.com/sitemap.xml'],
        ]);

        $suggestion->sitemapUrls()->create(['url' => 'https://example.com/DAF-truck']);
        $suggestion->sitemapUrls()->create(['url' => 'https://example.com/Scania']);
        $suggestion->sitemapUrls()->create(['url' => 'https://example.com/Another-DAF-model']);

        $response = $this->withoutMiddleware()
            ->postJson(route('statamic.cp.justbetter.suggestions', ['query' => 'DAF']));

        $response->assertStatus(200)->assertJson([
            'https://example.com/DAF-truck',
            'https://example.com/Another-DAF-model',
        ]);
    }

    #[Test]
    public function it_returns_nothing_if_no_sitemap(): void
    {
        SuggestionSetting::create([
            'collection_handle' => 'sitemap_en',
            'queryable_fields' => ['https://example.com/sitemap-fail.xml'],
        ]);

        $response = $this->withoutMiddleware()
            ->postJson(route('statamic.cp.justbetter.suggestions', ['query' => 'DAF']));

        $response->assertStatus(200)->assertJson([]);
    }
}
