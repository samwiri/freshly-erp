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
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip',
        'country',
        'tax_id',
        'customer_type',
        'billing_address',
        'delivery_address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'loyalty_points',
        'loyalty_tier',
        'lifetime_value',
        'total_orders',
        'last_order_date',
        'preferences',
        'tags',
        'notes',
    ];

    protected $casts = [
        'preferences' => 'array',
        'tags' => 'array',
        'loyalty_points' => 'integer',
        'total_orders' => 'integer',
        'last_order_date' => 'datetime',
        'lifetime_value' => 'decimal:2',
    ];

//relationship for the customer model
public function user(){
    return $this->belongsTo(User::class, 'user_id');
}
public function orders()
{
    return $this->hasMany(Order::class);
}
// Optional helper if needed in future
public static function generateCustomerCodeForId($id){
    return 'C' . str_pad((string)$id, 5, '0', STR_PAD_LEFT);
}

    
}
