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

    public function isMasuk(): bool
    {
        return $this->status === 'masuk';
    }

    public function isCuti(): bool
    {
        return $this->status === 'cuti';
    }

    public function isNonaktif(): bool
    {
        return $this->status === 'nonaktif';
    }

    public function scopeMasuk($query)
    {
        return $query->where('status', 'masuk');
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
