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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->enum('payment_type', ['Online', 'Offline']);
            $table->enum('payment_method', ['bkash', 'rocket', 'nagad', 'credit_card', 'cash', 'bank_transfer', 'other']);
            $table->string('acc_no', 100)->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('pay_type', ['booking', 'additional'])->default('booking');
            $table->string('transaction_id')->nullable();
            $table->text('reference')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
