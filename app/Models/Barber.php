<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barber extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'photo',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function jadwal()
    {
        return $this->hasMany(JadwalBarber::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
