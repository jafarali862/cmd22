<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicPrescription extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 
        'clinic_id', 
        'prescription', 
        'test',
        'name',
        'age',
        'gender', 
        'address', 
        'lat_long'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}
