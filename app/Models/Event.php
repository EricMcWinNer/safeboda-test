<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $table = "events";

    public function promoCodes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PromoCode::class, 'event_id');
    }

}
