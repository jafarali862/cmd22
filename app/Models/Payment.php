<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

     protected $fillable = [
        'pres_id',
        'status',
        'ref_no',
        'message',
        'accno',
        'trans_status',
        'amount',
        'remark',
     ];
}
