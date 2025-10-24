<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Order extends Model
{
    use SoftDeletes, HasFactory;
    protected $fillable = [
        'order_number',
        'customer_id',
        'employee_id',
        'service_type',
        'status',
        'priority',
        'weight',
        'subtotal',
        'tax',
        'discount',
        'total',
        'delivery_address',
        'pickup_time',
        'delivery_time',
        'payment_status',
        'special_instructions',
        'status_history',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'sub_total' => 'decimal:2',
        'pickup_time' => 'datetime',
        'delivery_time' => 'datetime',
        'status_history' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    //business logic for the order model

    public function updateStatus($newStatus,$userId, $notes = null)
    {
        $history = $this->status_history ?? [];

        $history[] = [
            'status' => $newStatus,
            'timestamp' => now(),
            'user_id' => $userId,
            'notes' => $notes,
        ];
        $this->status = $newStatus;
        $this->status_history = $history;
        $this->save();
    }
    public function calculateTotal()
    {
        $this->subtotal = $this->items->sum('total_price');
        $this->tax = $this->subtotal * 0.18;
        $this->total = $this->subtotal + $this->tax;
        $this->save();
    }
    protected static function boot(){
        parent::boot();
    
        static::creating(function($order){
            do {
                $count = Order::withTrashed()->whereYear('created_at', date('Y'))->count();
                $orderNumber = 'ORD-' . date('Y') . '-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
            } while (Order::where('order_number', $orderNumber)->exists());
            
            $order->order_number = $orderNumber;
        });
    }
}
