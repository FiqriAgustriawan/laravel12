<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Cek apakah kolom slug sudah ada, jika belum tambahkan
        if (!Schema::hasColumn('films', 'slug')) {
            Schema::table('films', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('judul');
            });
        }

        // Step 2: Generate slug untuk data yang sudah ada
        $films = DB::table('films')->whereNull('slug')->orWhere('slug', '')->get();
        
        foreach ($films as $film) {
            $slug = Str::slug($film->judul);
            $originalSlug = $slug;
            $counter = 1;

            // Pastikan slug unik
            while (DB::table('films')->where('slug', $slug)->where('id', '!=', $film->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Update slug untuk film ini
            DB::table('films')->where('id', $film->id)->update(['slug' => $slug]);
        }

        // Step 3: Tambahkan constraint unique setelah semua slug terisi
        try {
            Schema::table('films', function (Blueprint $table) {
                $table->unique('slug');
            });
        } catch (\Exception $e) {
            // Jika constraint sudah ada, abaikan error
        }

        // Step 4: Ubah kolom menjadi not null setelah semua terisi
        Schema::table('films', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('films', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};