<?php

namespace JustBetter\BardLinkSuggestions;

use JustBetter\BardLinkSuggestions\Actions\DispatchIndexSitemaps;
use JustBetter\BardLinkSuggestions\Actions\IndexSitemap;
use JustBetter\BardLinkSuggestions\Actions\IndexSitemaps;
use JustBetter\BardLinkSuggestions\Actions\PruneSitemap;
use JustBetter\BardLinkSuggestions\Actions\PruneSitemaps;
use JustBetter\BardLinkSuggestions\Actions\QueryEntries;
use JustBetter\BardLinkSuggestions\Actions\QuerySitemaps;
use JustBetter\BardLinkSuggestions\Commands\IndexSitemapsCommand;
use Statamic\CP\Navigation\Nav;
use Statamic\Facades\CP\Nav as NavFacade;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $routes = [
        'cp' => __DIR__.'/../routes/cp.php',
    ];

    /** @phpstan-ignore-next-line */
    protected $vite = [
        'publicDirectory' => 'resources/dist',
        'input' => [
            'resources/js/cp.js',
            'resources/css/cp.css',
        ],
    ];

    protected $commands = [
        IndexSitemapsCommand::class,
    ];

    public function boot(): void
    {
        parent::boot();

        $this->bootCpNav()
            ->bootViews()
            ->bootMigrations()
            ->bootActions();
    }

    protected function bootCpNav(): static
    {
        // @codeCoverageIgnoreStart
        NavFacade::extend(function (Nav $nav) {
            $nav->create('Suggestions settings')
                ->section('JustBetter')
                ->route('justbetter.suggestions.settings')
                ->icon('settings');
        });
        // @codeCoverageIgnoreEnd

        return $this;
    }

    protected function bootViews(): static
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'statamic-bard-suggestions');

        return $this;
    }

    protected function bootMigrations(): static
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        return $this;
    }

    protected function bootActions(): static
    {
        QueryEntries::bind();
        QuerySitemaps::bind();
        IndexSitemaps::bind();
        IndexSitemap::bind();
        PruneSitemaps::bind();
        PruneSitemap::bind();
        DispatchIndexSitemaps::bind();

        return $this;
    }
}
