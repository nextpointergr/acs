<?php

declare(strict_types=1);

namespace NextPointer\Acs;

use Illuminate\Support\ServiceProvider;
use NextPointer\Acs\Services\AcsClient;

class AcsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/Config/acs.php',
            'acs'
        );

        $this->app->singleton(AcsClient::class, fn () => new AcsClient());
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/Config/acs.php' => config_path('acs.php'),
        ], 'acs-config');
    }
}