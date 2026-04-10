<?php

namespace JustBetter\BardLinkSuggestions\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use JustBetter\BardLinkSuggestions\ServiceProvider;
use Statamic\Facades\Site;
use Statamic\Stache\Stores\UsersStore;
use Statamic\Testing\AddonTestCase;

abstract class TestCase extends AddonTestCase
{
    use RefreshDatabase;

    protected string $addonServiceProvider = ServiceProvider::class;

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set(
            'statamic.eloquent-driver',
            require __DIR__.'/../vendor/statamic/eloquent-driver/config/eloquent-driver.php'
        );

        /** @var array<string, mixed> $eloquentDriverConfig */
        $eloquentDriverConfig = $app['config']->get('statamic.eloquent-driver', []);

        collect($eloquentDriverConfig)
            ->filter(fn (array|string $config) => is_array($config) && isset($config['driver']))
            ->reject(fn (array|string $config, string $key) => $key === 'sites')
            ->each(fn (array|string $config, string $key) => $app['config']->set("statamic.eloquent-driver.{$key}.driver", 'eloquent'));

        Site::setSites([
            'en' => ['name' => 'English', 'locale' => 'en_US', 'url' => 'http://localhost/'],
        ]);

        $app['config']->set('auth.providers.users.driver', 'statamic');
        $app['config']->set('statamic.stache.watcher', false);
        $app['config']->set('statamic.stache.stores.users', [
            'class' => UsersStore::class,
            'directory' => __DIR__.'/__fixtures__/users',
        ]);

        $app['config']->set('statamic.editions.pro', true);

        $app['config']->set('cache.stores.outpost', [
            'driver' => 'file',
            'path' => storage_path('framework/cache/outpost-data'),
        ]);
    }

    protected function defineDatabaseMigrations()
    {
        parent::defineDatabaseMigrations();

        $this->loadMigrationsFrom(__DIR__.'/../vendor/statamic/eloquent-driver/database/migrations');
        $this->loadMigrationsFrom(__DIR__.'/../vendor/statamic/eloquent-driver/database/migrations/entries/2024_03_07_100000_create_entries_table.php');
    }

    protected function getPackageProviders($app)
    {
        return array_merge(parent::getPackageProviders($app), [
            \Statamic\Eloquent\ServiceProvider::class,
        ]);
    }
}
