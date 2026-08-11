<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'draw_id', 'customer_id', 'purchase_type', 'subtotal_amount', 'discount_amount', 'total_amount', 'status', 'payment_method', 'transaction_id', 'approval_transaction_id', 'voucher_path', 'notes'];

    protected function casts(): array
    {
        return [
            'subtotal_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function draw() { return $this->belongsTo(Draw::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function tickets() { return $this->hasMany(Ticket::class); }
}
