<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('posts', 'pillars')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->json('pillars')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('posts', 'pillars')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('pillars');
            });
        }
    }
};
