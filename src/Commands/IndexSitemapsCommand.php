<?php

namespace JustBetter\BardLinkSuggestions\Commands;

use Illuminate\Console\Command;
use JustBetter\BardLinkSuggestions\Jobs\DispatchIndexSitemapsJob;

class IndexSitemapsCommand extends Command
{
    protected $signature = 'statamic-bard-suggestions:index-sitemaps';

    protected $description = 'Indexes all the urls in the sitemaps to allow for suggestions.';

    public function handle(): void
    {
        DispatchIndexSitemapsJob::dispatch();
    }
}
