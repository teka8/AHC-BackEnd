<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('terms', function (Blueprint $table) {
            if (! Schema::hasColumn('terms', 'post_types')) {
                $table->json('post_types')->nullable()->after('parent_id');
            }
        });

        if (Schema::hasColumn('terms', 'post_types')) {
            DB::table('terms')
                ->whereNull('post_types')
                ->update(['post_types' => json_encode(['news'])]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('terms', 'post_types')) {
            Schema::table('terms', function (Blueprint $table) {
                $table->dropColumn('post_types');
            });
        }
    }
};
