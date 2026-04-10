<?php

namespace JustBetter\BardLinkSuggestions\Tests\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use JustBetter\BardLinkSuggestions\Http\Controllers\SuggestionsSettingsController;
use JustBetter\BardLinkSuggestions\Models\SuggestionSetting;
use JustBetter\BardLinkSuggestions\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Entries\Entry;
use Statamic\Facades\Collection as CollectionFacade;
use Statamic\Facades\Entry as EntryFacade;

class SuggestionsSettingsControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        CollectionFacade::make('terminology')->save();
        CollectionFacade::make('blog')->save();

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
            'keep-me' => Http::response($xml, 200),
            'remove-me' => Http::response($xml, 200),
            'new-en' => Http::response($xml, 200),
        ])->preventStrayRequests();
    }

    #[Test]
    public function it_returns_view_with_expected_data(): void
    {
        SuggestionSetting::create([
            'collection_handle' => 'sitemap_en',
            'queryable_fields' => ['keep-me', 'remove-me'],
        ]);

        $controller = app(SuggestionsSettingsController::class);
        /** @var View $response */
        $response = $controller->index();

        $this->assertEquals('statamic-bard-suggestions::settings', $response->getName());
        $viewData = $response->getData();

        $this->assertArrayHasKey('collections', $viewData);
        $this->assertTrue(
            $viewData['collections']->contains(fn ($collection) => $collection->handle() === 'terminology')
        );
        $this->assertArrayHasKey('collectionFields', $viewData);
        $this->assertArrayHasKey('terminology', $viewData['collectionFields']);
        $this->assertArrayHasKey('initialSettings', $viewData);
        $this->assertSame(['title'], $viewData['initialSettings']['terminology']);
        $this->assertArrayHasKey('sitemapUrls', $viewData['initialSettings']);
        $this->assertContains([
            'url' => 'keep-me',
            'site' => 'en',
        ], $viewData['initialSettings']['sitemapUrls']);
    }

    #[Test]
    public function it_saves_and_deletes_settings(): void
    {
        SuggestionSetting::create([
            'collection_handle' => 'sitemap_en',
            'queryable_fields' => ['keep-me', 'remove-me'],
        ]);

        $payload = [
            'queryable_collections' => ['blog', 'page'],
            'queryable_fields' => [
                'blog' => ['title', 'excerpt'],
                'page' => ['title'],
            ],
            'sitemap_urls' => [
                ['url' => 'keep-me', 'site' => 'en'],
                ['url' => 'new-en', 'site' => 'en'],
            ],
        ];

        $response = $this->withoutMiddleware()
            ->postJson(route('statamic.cp.justbetter.suggestions.settings.store'), $payload);

        $response->assertStatus(200)->assertJson(['success' => true]);
        /** @var SuggestionSetting $blog */
        $blog = SuggestionSetting::where('collection_handle', 'blog')->first();
        /** @var SuggestionSetting $page */
        $page = SuggestionSetting::where('collection_handle', 'page')->first();
        /** @var SuggestionSetting $sitemap */
        $sitemap = SuggestionSetting::where('collection_handle', 'sitemap_en')->first();

        $this->assertEqualsCanonicalizing(['title', 'excerpt'], $blog->queryable_fields);
        $this->assertEqualsCanonicalizing(['title'], $page->queryable_fields);
        $this->assertEqualsCanonicalizing(['keep-me', 'new-en'], $sitemap->queryable_fields);
    }
}
