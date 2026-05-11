<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Auth\Admin as Authenticatable;

class Admin extends Authenticatable
{
    protected $guarded = ['id'];
}
