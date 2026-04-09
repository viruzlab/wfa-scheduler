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
        Schema::create('wfa_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->constrained('dosens')->onDelete('cascade');
            $table->date('booking_date');
            $table->integer('week_number');
            $table->integer('year');
            $table->timestamps();

            // Aturan: 1 dosen maksimal 1 kali seminggu
            $table->unique(['dosen_id', 'week_number', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wfa_bookings');
    }
};
