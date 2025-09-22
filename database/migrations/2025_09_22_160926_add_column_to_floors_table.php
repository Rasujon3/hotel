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
        Schema::table('floors', function (Blueprint $table) {
            $table->foreignId('building_id')->nullable()->after('user_id')->constrained('buildings')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->after('status')->constrained('users')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->after('status')->constrained('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('floors', function (Blueprint $table) {
            //
        });
    }
};
