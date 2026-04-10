<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use JustBetter\BardLinkSuggestions\Models\SuggestionSetting;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sitemap_urls', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(SuggestionSetting::class);
            $table->string('url')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sitemap_urls');
    }
};
