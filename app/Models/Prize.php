<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prize extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'draw_id', 'name', 'description', 'image_path', 'price', 'active'];
    public function draw() { return $this->belongsTo(Draw::class); }
    public function tickets() { return $this->belongsToMany(Ticket::class, 'ticket_prizes')->withPivot('unit_price'); }
}
