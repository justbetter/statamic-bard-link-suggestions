<?php

namespace JustBetter\BardLinkSuggestions\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use JustBetter\BardLinkSuggestions\Contracts\PrunesSitemaps;

class PruneSitemapsJob implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array<int, string>  $sitemaps
     */
    public function __construct(
        protected array $sitemaps
    ) {}

    public function handle(PrunesSitemaps $prunesSitemaps): void
    {
        $prunesSitemaps->prune($this->sitemaps);
    }
}
