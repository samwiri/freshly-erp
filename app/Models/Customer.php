<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
class Customer extends Model
{
    use SoftDeletes, HasFactory;
    /**
     * 
    */
    protected $fillable = [
        'user_id',
        'customer_code',
        'company_name',
        'customer_type',
        'billing_address',
        'delivery_address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'loyalty_points',
        'loyalty_tier',
    ];

    protected $casts = [
        'preferences' => 'array',
        'loyalty_points' => 'string',
        'total_orders' => 'integer',
        'last_order_date' => 'datetime',
        'lifetime_value' => 'decimal:2',
    ];

//relationship for the customer model
public function user(){
    return $this->belongsTo(User::class);
}

    
}
