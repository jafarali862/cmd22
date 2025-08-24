<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pharmacy extends Model
{
    use HasFactory;

    protected $fillable = [
        'pharmacy_name',
        'pharmacy_address',
        'city',
        'phone_number',
        'email',
        'pharmacy_photo'
    ];

   
}
