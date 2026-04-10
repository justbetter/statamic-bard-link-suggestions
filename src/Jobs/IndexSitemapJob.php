<?php

namespace JustBetter\BardLinkSuggestions\Jobs;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use JustBetter\BardLinkSuggestions\Contracts\IndexesSitemap;
use JustBetter\BardLinkSuggestions\Models\SuggestionSetting;

class IndexSitemapJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public function __construct(
        protected SuggestionSetting $setting,
        protected string $url
    ) {}

    public function handle(IndexesSitemap $indexesSitemap): void
    {
        $indexesSitemap->index($this->setting, $this->url);
    }

    public function uniqueId(): string
    {
        return (string) $this->setting->id.'-'.$this->url;
    }
}
