<?php

namespace JustBetter\BardLinkSuggestions\Contracts;

interface PrunesSitemaps
{
    /**
     * @param  array<int, string>  $sitemaps
     */
    public function prune(array $sitemaps): void;
}
