<?php

namespace BlueSea\Semaphore;

use BlueSea\Semaphore\Commands\SemaphorePublish;
use Illuminate\Support\ServiceProvider;
use BlueSea\Semaphore\Contracts\Semaphore;
use BlueSea\Semaphore\Facades\Semaphore as SemaphoreFacade;

class SemaphoreServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $this->publishes([
            $this->resourcePath('config/semaphore.php') => config_path('semaphore.php')
        ], 'semaphore-config');

        $this->commands([
            SemaphorePublish::class,
        ]);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    public function resourcePath($res)
    {
        return __DIR__ . '/resources/' . $res;
    }


}
