<?php

namespace Devadze\BogPay\Facades;

use Devadze\BogPay\BogLoan as BogLoanService;
use Illuminate\Support\Facades\Facade;

class BogLoan extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BogLoanService::class;
    }
}
