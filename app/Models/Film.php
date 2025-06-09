<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

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

    /**
     * Upload and save poster image
     */
    public function uploadPoster(UploadedFile $file)
    {
        // Delete old poster if exists
        if ($this->poster_url) {
            $this->deletePoster();
        }

        // Generate unique filename
        $filename = 'film_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        
        // Store file in public disk under film-posters directory
        $path = $file->storeAs('film-posters', $filename, 'public');
        
        // Update model with just the path (not full URL)
        $this->update(['poster_url' => $path]);
        
        return $path;
    }
    
    /**
     * Delete poster file
     */
    public function deletePoster()
    {
        if ($this->poster_url) {
            // Get the original database value (not the accessor)
            $originalValue = $this->getOriginal('poster_url');
            
            if ($originalValue && Storage::disk('public')->exists($originalValue)) {
                Storage::disk('public')->delete($originalValue);
            }
        }
    }
    
    /**
     * Get full URL for poster
     */
    public function getPosterUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }
        
        // If it's already a full URL, return as is
        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }
        
        // Convert storage path to public URL
        return asset('storage/' . $value);
    }

    public function pemesanans()
    {
        return $this->hasMany(Pemesanan::class);
    }

    /**
     * Override the route key name - use slug for public routes only
     * For admin routes, we'll use ID by default
     */
    public function getRouteKeyName()
    {
        // Check if current route is admin route
        if (request()->is('api/admin/*')) {
            return 'id'; // Use ID for admin routes
        }
        return 'slug'; // Use slug for public routes
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
        
        // Delete poster when model is deleted
        static::deleting(function ($film) {
            $film->deletePoster();
        });
    }
}