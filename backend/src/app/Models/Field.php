<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Field extends Model
{
    //
    use HasFactory;

    protected $fillable = ['name', 'type', 'thumbnail', 'price_per_hour', 'description'];
    protected $table = 'fields';

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function fieldImages(): HasMany
    {
        return $this->hasMany(FieldImage::class);
    }
}
