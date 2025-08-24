<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PharmacyMedicine extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'pharmacy_prescription_id',
        'medicine_name',
        'quantity',
        'amount',
        'total',
        'start_time_1',
        'end_time_1',
        'start_time_2',
        'end_time_2',
        'start_time_3',
        'end_time_3',
        'req_unit',
        'avail_unit',
        'reffrnce'
    ];

    public function prescription()
    {
        return $this->belongsTo(PharmacyPrescription::class, 'pharmacy_prescription_id');
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'id');
    }
}
