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
        Schema::table('hotels', function (Blueprint $table) {
            $table->string('package_end_date')->nullable()->after('long');
            $table->string('package_start_date')->nullable()->after('long');
            $table->enum('status', ['Active', 'Inactive'])->default('Inactive')->nullable()->after('long');
            $table->string('package_id')->nullable()->after('long');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            //
        });
    }
};
