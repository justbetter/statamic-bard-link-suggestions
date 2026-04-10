<?php

namespace JustBetter\BardLinkSuggestions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SitemapUrl extends Model
{
    protected $table = 'sitemap_urls';

    protected $guarded = ['id'];

    /**
     * @return BelongsTo<SuggestionSetting, $this>
     */
    public function setting(): BelongsTo
    {
        return $this->belongsTo(SuggestionSetting::class, 'suggestion_setting_id');
    }
}
