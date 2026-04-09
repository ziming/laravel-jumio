<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio;

use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Ziming\LaravelJumio\Auth\JumioTokenStore;
use Ziming\LaravelJumio\Contracts\JumioClient;

class LaravelJumioServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-jumio')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(JumioTokenStore::class, function ($app): JumioTokenStore {
            return new JumioTokenStore(
                $app->make(CacheFactory::class),
                $app->make(ConfigRepository::class),
            );
        });

        $this->app->singleton(JumioManager::class, function ($app): JumioManager {
            return new JumioManager(
                $app->make(ConfigRepository::class),
                $app->make(JumioTokenStore::class),
            );
        });

        $this->app->singleton(LaravelJumio::class, function ($app): LaravelJumio {
            return new LaravelJumio(
                $app->make(JumioManager::class),
            );
        });

        $this->app->alias(JumioManager::class, JumioClient::class);
    }
}
