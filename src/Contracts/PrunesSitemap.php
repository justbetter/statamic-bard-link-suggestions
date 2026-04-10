<?php

namespace JustBetter\BardLinkSuggestions\Contracts;

interface PrunesSitemap
{
    public function prune(string $sitemap): void;
}
