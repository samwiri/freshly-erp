<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
class OrderItem extends Model
{
    use SoftDeletes, HasFactory;
    protected $fillable = [
        'order_id',
        'item_type',
        'service_type',
        'description',
        'quantity',
        'unit_price',
        'price',
        'total_price',
        'special_instructions',
        'metadata',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function order(){
        return $this->belongsTo(Order::class, 'order_id');
    }
    public function serviceItem(){
        return $this->belongsTo(ServiceItem::class, 'service_item_id');
    }
    protected static function boot(){
        parent::boot();
        
        static::saving(function($item){
            // Sync price and unit_price - if price is set, use it; otherwise use unit_price
            if (isset($item->price) && $item->price !== null) {
                $item->unit_price = $item->price;
            } elseif (isset($item->unit_price) && $item->unit_price !== null) {
                $item->price = $item->unit_price;
            }
            
            // Calculate total_price using unit_price (or price if unit_price is null)
            $unitPrice = $item->unit_price ?? $item->price ?? 0;
            $item->total_price = $item->quantity * $unitPrice;
        });
        
        static::saved(function($item){
            // Reload the order to ensure it's fresh
            $order = $item->order()->first();
            if ($order) {
                $order->calculateTotal();
            }
        });
    
        static::deleted(function($item){
            $order = $item->order()->first();
            if ($order) {
                $order->calculateTotal();
            }
        });
    }
}
