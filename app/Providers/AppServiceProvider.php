<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Banking\Core\Interfaces\AccountRepositoryInterface;
use App\Modules\Banking\Infrastructure\Repositories\InMemoryAccountRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            AccountRepositoryInterface::class,
            InMemoryAccountRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
