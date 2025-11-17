<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('media', 'folder_id')) {
            Schema::table('media', function (Blueprint $table) {
                $table->foreignId('folder_id')
                    ->nullable()
                    ->after('collection_name')
                    ->constrained('media_folders')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('media', 'folder_id')) {
            Schema::table('media', function (Blueprint $table) {
                $table->dropForeign(['folder_id']);
                $table->dropColumn('folder_id');
            });
        }
    }
};
