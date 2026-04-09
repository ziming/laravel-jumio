<?php

declare(strict_types=1);

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('it does not use the laravel http facade')
    ->expect('Ziming\\LaravelJumio')
    ->not->toUse('Illuminate\\Support\\Facades\\Http');
