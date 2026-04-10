<?php

namespace JustBetter\BardLinkSuggestions\Observers;

use JustBetter\BardLinkSuggestions\Jobs\IndexSitemapsJob;
use JustBetter\BardLinkSuggestions\Jobs\PruneSitemapsJob;
use JustBetter\BardLinkSuggestions\Models\SuggestionSetting;

class SuggestionSettingObserver
{
    public function created(SuggestionSetting $setting): void
    {
        $this->dispatchIndex($setting);
    }

    public function updated(SuggestionSetting $setting): void
    {
        $this->dispatchIndex($setting);
    }

    public function updating(SuggestionSetting $setting): void
    {
        $new = $setting->getAttribute('queryable_fields');
        $old = $setting->getOriginal('queryable_fields');
        $toBePruned = array_diff($old, $new);

        PruneSitemapsJob::dispatch($toBePruned);
    }

    protected function dispatchIndex(SuggestionSetting $setting): void
    {
        if (str($setting->collection_handle)->startsWith('sitemap_')) {
            IndexSitemapsJob::dispatch($setting);
        }
    }
}
