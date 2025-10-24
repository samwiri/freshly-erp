<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'invoice_number',
        'order_id',
        'customer_id',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax',
        'discount',
        'total',
        'status',
        'payment_terms',
        'sent_at',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function generateInvoiceNumber()
    {
        $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
        return $invoiceNumber;
    }


    
    public function markAsPaid()
    {
        $this->status = 'paid';
        $this->paid_at = now();
        $this->save();
    }
    protected static function boot()
    {
        parent::boot();
        static::creating(function (
            $invoice
        ) {
            $year = date('Y');
            $count = Invoice::whereYear('created_at', $year)->count() + 1;
            $invoice->invoice_number = 'INV-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
            if (!$invoice->invoice_date) {
                $invoice->invoice_date = now();
            }
            if (!$invoice->due_date) {
                $invoice->due_date = $invoice->invoice_date->addDays(30);
            }
        });
    }
}
