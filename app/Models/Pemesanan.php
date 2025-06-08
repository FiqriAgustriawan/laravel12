<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'film_id',
        'jumlah_tiket',
        'total_harga',
        'waktu_pemesanan',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total_harga' => 'decimal:2',
            'waktu_pemesanan' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function film()
    {
        return $this->belongsTo(Film::class);
    }
}
