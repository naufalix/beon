<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{

    // public static function getAppName(){
    //     return str_replace("_", " ", env('APP_NAME'));
    // }

    public static $data_meta = [
        // 'app_name'    => self::getAppName(),
        'description' => 'Tripsi Tour & Organizer menyediakan layanan Private Trip, Open Trip, Outing, Gathering, hingga Rentcar & Shuttle untuk destinasi Malang, Bromo, Tumpak Sewu, dan Banyuwangi.',
        'keywords'    => 'Tripsi Tour, Travel Malang, Trip Bromo, Open Trip Bromo, Private Trip Malang, Tumpak Sewu Tour, Banyuwangi Tour, Rentcar Malang, Shuttle Malang, Tour Organizer Malang, Wisata Bromo, Paket Wisata Malang',
        'type'        => 'website',
        'title'       => 'Tripsi Tour & Organizer - Private Trip, Open Trip & Rentcar',
        'site_name'   => 'Tripsi Tour & Organizer',
        'image'       => '/assets/img/logo.webp',  
        'url'         => 'https://tripsi.id'
    ];

}
