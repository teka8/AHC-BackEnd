<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarship_application_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('scholarship_applications')->onDelete('cascade');
            $table->enum('status', ['draft', 'submitted', 'under-review', 'shortlisted', 'interviewed', 'accepted', 'rejected', 'withdrawn']);
            $table->text('note')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('timestamp')->useCurrent();
            
            $table->index(['application_id', 'timestamp'], 'sash_app_timestamp_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarship_application_status_history');
    }
};
