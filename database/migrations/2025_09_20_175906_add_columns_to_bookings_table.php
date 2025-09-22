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
        Schema::table('bookings', function (Blueprint $table) {
//            $table->string('payment_type', 50)->nullable()->after('hotel_id');
//            $table->dropColumn('booking_start_date');
//            $table->dropColumn('booking_end_date');
//            $table->dropColumn('check_in');
//            $table->dropColumn('check_out');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            //
        });
    }
};
