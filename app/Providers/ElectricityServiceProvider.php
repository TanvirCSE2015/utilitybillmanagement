<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ElectricityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(
            database_path('migrations/electricity')
        );
    }
}
