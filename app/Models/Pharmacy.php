<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pharmacy extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'pharmacy_name',
        'pharmacy_address',
        'city',
        'phone_number',
        'types',
        'email',
        'pharmacy_photo',
        'upi',
        'qr_code',
        'account_holder_name',
        'account_no',
        'ifsc'
    ];

    public function prescriptions()
    {
        return $this->hasMany(PharmacyPrescription::class, 'pharmacy_id');
    }
    

   
}
