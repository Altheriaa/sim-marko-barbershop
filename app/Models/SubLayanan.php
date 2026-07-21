<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubLayanan extends Model
{
    protected $table = 'sub_layanan';

    protected $fillable = [
        'layanan_id',
        'nama',
    ];

    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }
}
