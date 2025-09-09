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
        Schema::table('rooms', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->after('description');
            $table->decimal('booking_price', 10, 2)->nullable()->after('description');
            $table->timestamp('start_booking_time')->nullable()->after('description');
            $table->timestamp('end_booking_time')->nullable()->after('description');
            $table->enum('current_status', ['available', 'booked', 'maintenance', 'occupied'])->default('available')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            //
        });
    }
};
