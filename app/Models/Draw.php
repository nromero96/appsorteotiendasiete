<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Draw extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'title', 'draw_date', 'draw_time', 'transmission', 'flyer_path', 'combo_discount'];

    protected function casts(): array
    {
        return [
            'draw_date' => 'date',
            'combo_discount' => 'decimal:2',
        ];
    }

    public function prizes() { return $this->hasMany(Prize::class); }
    public function sales() { return $this->hasMany(Sale::class); }
    public function tickets() { return $this->hasMany(Ticket::class); }

    public function ticketRegistrationDeadline(): ?Carbon
    {
        if (!$this->draw_date || !$this->draw_time) {
            return null;
        }

        return Carbon::parse(
            $this->draw_date->format('Y-m-d').' '.$this->draw_time,
            config('app.timezone')
        )->subMinutes(30);
    }

    public function isTicketRegistrationClosed(): bool
    {
        $deadline = $this->ticketRegistrationDeadline();

        return $deadline !== null && now()->greaterThanOrEqualTo($deadline);
    }

    public function isPast(): bool
    {
        if (! $this->draw_date) {
            return false;
        }

        $scheduledAt = Carbon::parse(
            $this->draw_date->format('Y-m-d').' '.($this->draw_time ?: '23:59:59'),
            config('app.timezone')
        );

        return now(config('app.timezone'))->greaterThan($scheduledAt);
    }
}
