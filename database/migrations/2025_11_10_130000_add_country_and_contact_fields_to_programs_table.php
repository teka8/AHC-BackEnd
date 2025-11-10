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
        Schema::table('programs', function (Blueprint $table) {
            $table->string('country')->nullable()->after('host');
            $table->string('contact_name')->nullable()->after('description');
            $table->text('contact_bio')->nullable()->after('contact_name');
            $table->text('contact_details')->nullable()->after('contact_bio');
            $table->text('partners_involved')->nullable()->after('contact_details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn([
                'country',
                'contact_name',
                'contact_bio',
                'contact_details',
                'partners_involved',
            ]);
        });
    }
};
