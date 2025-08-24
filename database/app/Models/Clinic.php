<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_name',
        'tests',
        'clinic_address',
        'clinic_photo',
        'city',
        'phone_number',
        'email'
    ];
}
