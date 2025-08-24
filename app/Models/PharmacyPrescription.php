<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PharmacyPrescription extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'pharmacy_id','name', 'prescription', 'delivery_address', 'lat_long','payment_method','expect_date','total_amount','delivery_id','delivery_coordinates','receipt','confirm_payment'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class, 'pharmacy_id');
    }

    public function deliveryAgent()
    {
        return $this->belongsTo(User::class, 'delivery_id');
    }
}
