<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) If the default Laravel notifications table exists, rename it to avoid collision
        if (Schema::hasTable('notifications')) {
            // Heuristic: if it has notifiable columns or uuid id, it's likely the default table
            $hasNotifiableId = Schema::hasColumn('notifications', 'notifiable_id');
            $hasUserId = Schema::hasColumn('notifications', 'user_id');

            if ($hasNotifiableId && !$hasUserId) {
                // Rename the default table to system_notifications (keep any existing data)
                Schema::rename('notifications', 'system_notifications');
            }
        }

        // 2) Create our application notifications table if it's missing
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
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
        // Drop our app notifications table if it exists
        if (Schema::hasTable('notifications')) {
            Schema::dropIfExists('notifications');
        }

        // If we had renamed the default table earlier, rename it back
        if (Schema::hasTable('system_notifications') && !Schema::hasTable('notifications')) {
            Schema::rename('system_notifications', 'notifications');
        }
    }
};
