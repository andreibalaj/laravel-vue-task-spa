<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\TaskService;
use Kreait\Firebase\Firestore;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
         $this->app->singleton(Firestore::class, function () {
            return app('firebase.firestore');
        });

        $this->app->singleton(TaskService::class, function ($app) {
            return new TaskService($app->make(Firestore::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
