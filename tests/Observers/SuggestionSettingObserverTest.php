<?php

namespace JustBetter\BardLinkSuggestions\Tests\Observers;

use Illuminate\Support\Facades\Bus;
use JustBetter\BardLinkSuggestions\Jobs\IndexSitemapsJob;
use JustBetter\BardLinkSuggestions\Models\SuggestionSetting;
use JustBetter\BardLinkSuggestions\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SuggestionSettingObserverTest extends TestCase
{
    #[Test]
    public function it_dispatches_job(): void
    {
        Bus::fake();

        $suggestion = SuggestionSetting::create([
            'collection_handle' => 'sitemap_en',
            'queryable_fields' => ['https://example.com/sitemap.xml'],
        ]);

        Bus::assertDispatched(IndexSitemapsJob::class);
    }
}
