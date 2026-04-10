<?php

namespace JustBetter\BardLinkSuggestions\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use JustBetter\BardLinkSuggestions\Contracts\DispatchesIndexSitemaps;

class DispatchIndexSitemapsJob implements ShouldQueue
{
    use Queueable;

    public function handle(DispatchesIndexSitemaps $dispatchesIndexSitemaps): void
    {
        $dispatchesIndexSitemaps->dispatch();
    }
}
