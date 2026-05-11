<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function bill()
    {
        return $this->belongsTo(PaymentBill::class, 'bill_id');
    }
}
