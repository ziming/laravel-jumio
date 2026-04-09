<?php

namespace Ziming\LaravelJumio\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ziming\LaravelJumio\LaravelJumio
 */
class LaravelJumio extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Ziming\LaravelJumio\LaravelJumio::class;
    }
}
