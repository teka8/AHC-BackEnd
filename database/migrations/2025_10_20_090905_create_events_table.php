<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->time('start_time');        
            $table->time('end_time')->nullable();
            $table->date('event_date');
            $table->text('location')->nullable();
            $table->text('google_map_location_link')->nullable();
            $table->string('category')->nullable();
            $table->boolean('register_on_site')->default(0);
            $table->text('registration_link')->nullable();
            $table->decimal('cost_amount', 8, 2)->nullable();
            $table->string('event_type'); // VIRTUAL(Online) and in-person
            $table->string('target_audience')->nullable(); // e.g., public, for_students, for_staff, for_employees, for_alumni, and also custom audience
            $table->string('status')->default('draft');
            $table->text('image_url')->nullable();
            $table->boolean('is_archived')->default(0);
            $table->Integer('created_by')->nullable();
            $table->Integer('approved_by')->nullable();
            $table->Integer('reviewed_by')->nullable();
            $table->Integer('archived_by')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
