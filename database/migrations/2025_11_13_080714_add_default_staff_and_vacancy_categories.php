<?php

use App\Models\Term;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('terms')) {
            return;
        }

        $attributes = [];
        if (Schema::hasColumn('terms', 'post_types')) {
            $attributes['post_types'] = ['announcement'];
        }

        foreach (['Staff', 'Vacancy'] as $name) {
            Term::query()->firstOrCreate(
                [
                    'slug' => Str::slug($name),
                    'taxonomy' => 'category',
                ],
                array_merge([
                    'name' => $name,
                    'description' => $name . ' posts',
                ], $attributes)
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('terms')) {
            return;
        }

        $query = Term::query()
            ->where('taxonomy', 'category')
            ->whereIn('slug', ['staff', 'vacancy'])
            ->doesntHave('posts');

        if (Schema::hasColumn('terms', 'post_types')) {
            $query->whereJsonContains('post_types', 'announcement');
        }

        $query->delete();
    }
};
