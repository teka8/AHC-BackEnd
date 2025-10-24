<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // If the default table is missing but a previously renamed table exists, restore it
        if (!Schema::hasTable('notifications') && Schema::hasTable('system_notifications')) {
            Schema::rename('system_notifications', 'notifications');
        }

        // If notifications table still doesn't exist, create the default Laravel notifications table
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Prefer leaving the default notifications table in place on rollback to avoid data loss
        // No action
    }
};
