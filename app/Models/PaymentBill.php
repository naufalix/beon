<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentBill extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'billing_month' => 'date',
        'amount' => 'decimal:2',
    ];

    public function house()
    {
        return $this->belongsTo(House::class);
    }

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function feeType()
    {
        return $this->belongsTo(FeeType::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'bill_id');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }
}
