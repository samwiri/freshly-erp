<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'payment_number',
        'invoice_id',
        'order_id',
        'amount',
        'payment_method',
        'status',
        'transaction_id',
        'reference_number',
        'payment_date',
        'metadata',
    ];
    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'metadata' => 'array',
    ];
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function processPayment()
    {
        //update the invoice 
        $totalPaid = $this->invoice->payments()
            ->where('status', 'completed')
            ->sum('amount');
        if ($totalPaid >= $this->invoice->total) {
            $this->invoice->markAsPaid();
        }
        //update the payment status
        $this->order->payment_status = 'paid';
        $this->order->save();
    }
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($payment) {
            $payment->payment_number = 'PAY-' . date('Ymd') . '-' . strtoupper(uniqid());
            $payment->transaction_id = 'TXN-' . date('Ymd') . '-' . strtoupper(uniqid());
        });
        static::updating(function ($payment) {
            if ($payment->status === 'completed') {
                $payment->processPayment();
            }
        });
    }
}
