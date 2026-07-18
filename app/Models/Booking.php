<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'booking';

    protected $fillable = [
        'user_id',
        'barber_id',
        'layanan_id',
        'jadwal_id',
        'sumber',
        'qr_code',
        'status',
        'waktu_checkin',
        'waktu_checkout',
        'dibuat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'waktu_checkin' => 'datetime',
            'waktu_checkout' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalBarber::class, 'jadwal_id');
    }

    public function transaksi()
    {
        return $this->hasOne(Transaksi::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}
