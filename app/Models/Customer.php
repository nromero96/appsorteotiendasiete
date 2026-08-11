<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['full_name', 'phone', 'document_number'];
    public function sales() { return $this->hasMany(Sale::class); }
    public function tickets() { return $this->hasMany(Ticket::class); }
}
