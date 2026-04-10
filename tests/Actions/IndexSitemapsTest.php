<?php

namespace JustBetter\BardLinkSuggestions\Tests\Actions;

use Illuminate\Support\Facades\Bus;
use JustBetter\BardLinkSuggestions\Actions\IndexSitemaps;
use JustBetter\BardLinkSuggestions\Jobs\IndexSitemapJob;
use JustBetter\BardLinkSuggestions\Models\SuggestionSetting;
use JustBetter\BardLinkSuggestions\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class IndexSitemapsTest extends TestCase
{
    #[Test]
    public function it_dispatches_job(): void
    {
        Bus::fake();

        $suggestion = SuggestionSetting::create([
            'collection_handle' => 'sitemap_en',
            'queryable_fields' => ['https://example.com/sitemap.xml'],
        ]);

        $action = app(IndexSitemaps::class);

        $action->index($suggestion);

        Bus::assertDispatched(IndexSitemapJob::class);
    }
}
