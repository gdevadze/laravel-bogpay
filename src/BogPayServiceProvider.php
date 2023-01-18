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
        $this->publishes([
            __DIR__ . '/../config/bogpay.php' => config_path('bogpay.php'),
        ], 'config');
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
