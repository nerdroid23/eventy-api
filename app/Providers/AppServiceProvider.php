<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Events\MigrationsEnded;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->listenToMigrationsEndedEvent();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    protected function listenToMigrationsEndedEvent()
    {
        if (App::isLocal()) {
            Event::listen(MigrationsEnded::class, function ($event) {
                Artisan::call('ide-helper:models', [
                    '--write' => true,
                    '--reset' => true,
                    '--no-interaction' => true,
                ]);
            });
        }
    }
}
