<?php

namespace Devadze\BogPay\Facades;

use Devadze\BogPay\BogPay as BogPayService;
use Illuminate\Support\Facades\Facade;

class BogPay extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BogPayService::class;
    }
}
