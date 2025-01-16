<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    //
    protected $fillable = ['booking_id', 'amount', 'payment_date', 'payment_method', 'status'];
    protected $table = 'payments';

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
