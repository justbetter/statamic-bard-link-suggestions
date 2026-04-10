<?php

namespace JustBetter\BardLinkSuggestions\Contracts;

use JustBetter\BardLinkSuggestions\Models\SuggestionSetting;

interface IndexesSitemap
{
    public function index(SuggestionSetting $setting, string $url): void;
}
