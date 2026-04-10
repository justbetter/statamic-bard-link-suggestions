<?php

namespace JustBetter\BardLinkSuggestions\Jobs;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use JustBetter\BardLinkSuggestions\Contracts\IndexesSitemaps;
use JustBetter\BardLinkSuggestions\Models\SuggestionSetting;

class IndexSitemapsJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public function __construct(
        protected SuggestionSetting $setting
    ) {}

    public function handle(IndexesSitemaps $indexesSitemaps): void
    {
        $indexesSitemaps->index($this->setting);
    }

    public function uniqueId(): int
    {
        return $this->setting->id;
    }
}
