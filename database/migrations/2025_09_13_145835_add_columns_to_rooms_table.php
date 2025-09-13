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
            $table->string('icon')->nullable()->after('status');
            $table->decimal('system_commission', 10, 2)->nullable()->after('status');
            $table->decimal('discount_amount', 10, 2)->nullable()->after('status');
            $table->enum('num_of_beds', ['1', '2', '3'])->nullable()->after('bed_type');
            $table->enum('room_type', ['AC', 'Non-AC'])->nullable()->after('bed_type');
            $table->renameColumn('price', 'rent');
            $table->renameColumn('description', 'view');
            $table->dropColumn('has_ac');
            $table->dropColumn('calculate_booking_price');



//            $table->dropColumn('description');
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
