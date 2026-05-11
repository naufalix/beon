<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    protected $guarded = ['id'];

    public function residents()
    {
        return $this->hasMany(Resident::class);
    }

    public function activeResidents()
    {
        return $this->hasMany(Resident::class)->where('is_active_resident', true);
    }

    public function headOfFamily()
    {
        return $this->hasOne(Resident::class)->where('is_head_of_family', true);
    }
}
