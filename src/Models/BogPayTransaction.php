<?php

namespace Devadze\BogPay\Models;

use Illuminate\Database\Eloquent\Model;

class BogPayTransaction extends Model
{
    protected $fillable = [
        'locale',
        'model_id',
        'model_type',
        'amount',
        'order_id',
        'is_paid',
        'status',
        'completed_at'
    ];

    public function model()
    {
        return $this->morphTo();
    }
}
