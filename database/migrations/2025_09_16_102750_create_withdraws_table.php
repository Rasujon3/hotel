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
        Schema::create('withdraws', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('hotel_id')->nullable()->constrained('hotels')->onDelete('cascade');
            $table->foreignId('withdrawal_method_id')->nullable()->constrained('withdrawal_methods')->onDelete('cascade');
            $table->string('title', 100)->nullable();
            $table->string('payment_type', 100)->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->timestamp('withdraw_at')->nullable();
            $table->string('trx_id', 100)->nullable();
            $table->text('reference')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdraws');
    }
};
