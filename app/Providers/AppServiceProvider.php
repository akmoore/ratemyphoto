<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind('App\RMP\Interfaces\Photo', 'App\RMP\Repositories\PhotoRepo');
        $this->app->bind('App\RMP\Interfaces\Staff', 'App\RMP\Repositories\StaffRepo');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
