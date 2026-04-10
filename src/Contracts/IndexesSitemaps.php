<?php

namespace JustBetter\BardLinkSuggestions\Contracts;

use JustBetter\BardLinkSuggestions\Models\SuggestionSetting;

interface IndexesSitemaps
{
    public function index(SuggestionSetting $setting): void;
}
