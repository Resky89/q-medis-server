<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\LoketRepositoryInterface;
use App\Repositories\Eloquent\LoketRepository;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LoketRepositoryInterface::class, LoketRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
