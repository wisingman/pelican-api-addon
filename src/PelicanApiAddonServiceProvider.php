<?php

namespace Wising\PelicanApiAddon;

use Illuminate\Support\ServiceProvider;

class PelicanApiAddonServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }

}
