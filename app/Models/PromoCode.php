<?php


namespace App\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    use HasFactory;

    protected $table = "promo_codes";

    public function event(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function isActive(): bool
    {
        return (bool)$this->attributes['is_active'] && Carbon::parse($this->event->event_starts_at)->lessThanOrEqualTo(Carbon::now());
    }

    public function isNotExpired(): bool
    {
        $expiresAt = Carbon::parse($this->attributes['expires_at']);
        return $expiresAt->greaterThan(Carbon::now()) && Carbon::parse($this->event->event_ends_at)->greaterThan(Carbon::now());
    }

    public function isValid(): bool {
        return $this->isActive() && $this->isNotExpired();
    }




}
