<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Film extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'slug',
        'poster_url',
        'harga_tiket',
        'deskripsi',
        'tanggal_rilis',
        'durasi',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'harga_tiket' => 'decimal:2',
            'tanggal_rilis' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function pemesanans()
    {
        return $this->hasMany(Pemesanan::class);
    }

    /**
     * Override the route key name to use slug instead of id
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Generate slug from judul
     */
    public static function generateSlug($judul)
    {
        $slug = Str::slug($judul);
        $originalSlug = $slug;
        $counter = 1;

        // Check if slug already exists and increment if needed
        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Boot method to auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($film) {
            if (empty($film->slug)) {
                $film->slug = static::generateSlug($film->judul);
            }
        });

        static::updating(function ($film) {
            if ($film->isDirty('judul') && empty($film->slug)) {
                $film->slug = static::generateSlug($film->judul);
            }
        });
    }
}