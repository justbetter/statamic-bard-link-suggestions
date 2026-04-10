<?php

namespace JustBetter\BardLinkSuggestions\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JustBetter\BardLinkSuggestions\Observers\SuggestionSettingObserver;

/**
 * @property int $id
 * @property string $collection_handle
 * @property array<array-key, mixed> $queryable_fields
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
#[ObservedBy([SuggestionSettingObserver::class])]
class SuggestionSetting extends Model
{
    protected $table = 'suggestions_settings';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'queryable_fields' => 'array',
        ];
    }

    /**
     * @return HasMany<SitemapUrl, $this>
     */
    public function sitemapUrls(): HasMany
    {
        return $this->hasMany(SitemapUrl::class);
    }
}
