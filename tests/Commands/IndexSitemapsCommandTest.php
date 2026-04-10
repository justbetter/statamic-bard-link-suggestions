<?php

namespace JustBetter\BardLinkSuggestions\Tests\Commands;

use Illuminate\Support\Facades\Bus;
use JustBetter\BardLinkSuggestions\Jobs\DispatchIndexSitemapsJob;
use JustBetter\BardLinkSuggestions\Models\SuggestionSetting;
use JustBetter\BardLinkSuggestions\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class IndexSitemapsCommandTest extends TestCase
{
    #[Test]
    public function it_dispatches_jobs(): void
    {
        Bus::fake();

        SuggestionSetting::withoutEvents(fn () => SuggestionSetting::create([
            'collection_handle' => 'sitemap_en',
            'queryable_fields' => ['https://example.com/sitemap.xml'],
        ]));

        $this->artisan('statamic-bard-suggestions:index-sitemaps');

        Bus::assertDispatchedTimes(DispatchIndexSitemapsJob::class, 1);
    }
}
