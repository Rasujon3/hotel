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
        Schema::table('booking_details', function (Blueprint $table) {
            $table->string('status', 50)->nullable()->after('check_out')->default('confirmed');
            $table->decimal('rent',10, 2)->nullable()->default(0)->after('check_out');
            $table->string('day_count', 50)->nullable()->default(1)->after('check_out');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_details', function (Blueprint $table) {
            //
        });
    }
};
