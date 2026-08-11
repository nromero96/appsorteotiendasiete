<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'sale_id', 'draw_id', 'prize_id', 'customer_id', 'ticket_number', 'purchase_type', 'total_amount', 'status', 'payment_method', 'transaction_id', 'voucher_path', 'notes'];
    public function sale() { return $this->belongsTo(Sale::class); }
    public function draw() { return $this->belongsTo(Draw::class); }
    public function prize() { return $this->belongsTo(Prize::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function prizes() { return $this->belongsToMany(Prize::class, 'ticket_prizes')->withPivot('unit_price'); }
}
