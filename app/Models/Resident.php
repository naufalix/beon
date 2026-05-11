<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resident extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_married' => 'boolean',
        'is_active_resident' => 'boolean',
        'is_head_of_family' => 'boolean',
        'move_in_date' => 'date',
        'move_out_date' => 'date',
    ];

    public function house()
    {
        return $this->belongsTo(House::class);
    }
}
