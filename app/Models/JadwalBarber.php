<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalBarber extends Model
{
    protected $table = 'jadwal_barber';

    protected $fillable = [
        'barber_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'jadwal_id');
    }
}
