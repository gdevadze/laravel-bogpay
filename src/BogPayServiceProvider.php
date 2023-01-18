<?php

namespace Devadze\BogPay;

use Illuminate\Support\ServiceProvider;

class BogPayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(BogPay::class);
    }
}
