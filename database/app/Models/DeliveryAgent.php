<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryAgent extends Model
{
    use HasFactory;
    protected $fillable = [
        'delivery_agent_id',
        'address',
        'coordinates',
        'delivery_status',
        'customer_mob',
        'delivered_coordinates',
        'customer_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'delivery_agent_id');
    }
}
