<?php

namespace Devadze\BogPay\Models;

use Illuminate\Database\Eloquent\Model;

class BogPayLog extends Model
{
    protected $fillable = [
        'transaction_id',
        'order_id',
        'message',
        'payload',
    ];
}
