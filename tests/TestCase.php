<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Saloon\Laravel\SaloonServiceProvider;
use Spatie\LaravelData\LaravelDataServiceProvider;
use Spatie\LaravelData\Support\Creation\ValidationStrategy;
use Ziming\LaravelJumio\LaravelJumioServiceProvider;

class TestCase extends Orchestra
{
    /**
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            SaloonServiceProvider::class,
            LaravelDataServiceProvider::class,
            LaravelJumioServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('cache.default', 'array');
        $app['config']->set('data', [
            'date_format' => DATE_ATOM,
            'date_timezone' => null,
            'features' => [
                'cast_and_transform_iterables' => false,
                'ignore_exception_when_trying_to_set_computed_property_value' => false,
            ],
            'transformers' => [],
            'casts' => [],
            'rule_inferrers' => [],
            'normalizers' => [],
            'wrap' => null,
            'var_dumper_caster_mode' => 'disabled',
            'structure_caching' => [
                'enabled' => false,
                'directories' => [],
                'cache' => [
                    'store' => 'array',
                    'prefix' => 'laravel-data-tests',
                    'duration' => null,
                ],
                'reflection_discovery' => [
                    'enabled' => false,
                    'base_path' => base_path(),
                    'root_namespace' => null,
                ],
            ],
            'validation_strategy' => ValidationStrategy::OnlyRequests->value,
            'name_mapping_strategy' => [
                'input' => null,
                'output' => null,
            ],
            'ignore_invalid_partials' => false,
            'max_transformation_depth' => null,
            'throw_when_max_transformation_depth_reached' => false,
            'commands' => [
                'make' => [
                    'namespace' => 'Data',
                    'suffix' => 'Data',
                ],
            ],
            'livewire' => [
                'enable_synths' => false,
            ],
        ]);
        $app['config']->set('jumio', [
            'region' => 'amer-1',
            'client_id' => 'jumio-client-id',
            'client_secret' => 'jumio-client-secret',
            'user_agent' => 'patient-crab-tests/1.0',
            'callback_url' => 'https://example.test/jumio/callback',
            'timeout' => 30,
            'connect_timeout' => 10,
            'retry' => [
                'tries' => 1,
                'interval_ms' => 0,
                'exponential_backoff' => false,
            ],
            'cache' => [
                'store' => 'array',
                'prefix' => 'jumio-tests',
                'token_refresh_buffer' => 300,
            ],
        ]);
    }
}
