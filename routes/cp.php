<?php

use Illuminate\Support\Facades\Route;
use JustBetter\BardLinkSuggestions\Http\Controllers\SuggestionsController;
use JustBetter\BardLinkSuggestions\Http\Controllers\SuggestionsSettingsController;

Route::post('/api/suggestions', SuggestionsController::class)
    ->name('justbetter.suggestions');

Route::post('/suggestions/settings', [SuggestionsSettingsController::class, 'store'])
    ->name('justbetter.suggestions.settings.store');

Route::get('/suggestions/settings', [SuggestionsSettingsController::class, 'index'])
    ->name('justbetter.suggestions.settings');
