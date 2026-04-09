<?php

namespace Ziming\LaravelJumio;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Ziming\LaravelJumio\Commands\LaravelJumioCommand;

class LaravelJumioServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-jumio')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_jumio_table')
            ->hasCommand(LaravelJumioCommand::class);
    }
}
