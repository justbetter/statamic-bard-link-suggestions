<?php

namespace JustBetter\BardLinkSuggestions\Jobs;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use JustBetter\BardLinkSuggestions\Contracts\PrunesSitemap;

class PruneSitemapJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $sitemap
    ) {}

    public function handle(PrunesSitemap $prunesSitemap): void
    {
        $prunesSitemap->prune($this->sitemap);
    }

    public function uniqueId(): string
    {
        return $this->sitemap;
    }
}
