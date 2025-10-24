<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // If a custom notifications table exists under the old name, rename it to app_notifications
        if (Schema::hasTable('notifications')) {
            $hasUserId = Schema::hasColumn('notifications', 'user_id');
            $hasTitle = Schema::hasColumn('notifications', 'title');
            $hasMessage = Schema::hasColumn('notifications', 'message');

            // Only rename if it's the custom schema (not the default Laravel one)
            if ($hasUserId && $hasTitle && $hasMessage) {
                if (!Schema::hasTable('app_notifications')) {
                    Schema::rename('notifications', 'app_notifications');
                }
            }
        }

        // If app_notifications still doesn't exist, create it
        if (!Schema::hasTable('app_notifications')) {
            Schema::create('app_notifications', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('type');
                $table->string('title');
                $table->text('message');
                $table->json('data')->nullable();
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'read_at']);
            });
        }
    }

    public function down(): void
    {
        // On rollback, if notifications table does not exist, rename back
        if (!Schema::hasTable('notifications') && Schema::hasTable('app_notifications')) {
            Schema::rename('app_notifications', 'notifications');
        }
    }
};
