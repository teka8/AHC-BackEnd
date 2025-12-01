<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $universities = [
            'Debre Berhan University',
            'Wollo University',
            'Woldia University',
            'Wolkite University',
        ];

        foreach ($universities as $university) {
            $exists = DB::table('terms')
                ->where('name', $university)
                ->where('taxonomy', 'category')
                ->exists();

            if (!$exists) {
                DB::table('terms')->insert([
                    'name' => $university,
                    'slug' => Str::slug($university),
                    'taxonomy' => 'category',
                    'description' => "News category for {$university}",
                    'post_types' => json_encode(['news']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $universities = [
            'Debre Berhan University',
            'Wollo University',
            'Woldia University',
            'Wolkite University',
        ];

        DB::table('terms')
            ->whereIn('name', $universities)
            ->where('taxonomy', 'category')
            ->delete();
    }
};
