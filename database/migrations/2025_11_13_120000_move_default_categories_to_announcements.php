<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('terms')) {
            return;
        }

        DB::table('terms')
            ->where('taxonomy', 'category')
            ->where('slug', 'uncategorized')
            ->update(['post_types' => json_encode(['announcement'])]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('terms')) {
            return;
        }

        DB::table('terms')
            ->where('taxonomy', 'category')
            ->where('slug', 'uncategorized')
            ->update(['post_types' => json_encode(['news'])]);
    }
};
