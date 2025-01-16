<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    //
    use HasFactory;

    protected $fillable = ['user_id', 'field_id', 'booking_date', 'start_time', 'end_time', 'total_price'];
    protected $table = 'bookings';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
