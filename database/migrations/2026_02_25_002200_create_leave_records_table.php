<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Earned Leave Credits Balance
            $table->date('as_of_date')->nullable();
            $table->decimal('el_vl', 8, 2)->nullable()->comment('Earned Leave VL balance');
            $table->decimal('el_sl', 8, 2)->nullable()->comment('Earned Leave SL balance');

            // No. of Days Without Pay
            $table->decimal('no_pay_vl', 8, 2)->nullable();
            $table->decimal('no_pay_sl', 8, 2)->nullable();
            $table->decimal('no_pay_total', 8, 2)->nullable();
            $table->json('no_pay_dates')->nullable();

            // No. of Days of Undertime/Tardy Without Pay
            $table->integer('undertime_hours')->nullable();
            $table->integer('undertime_minutes')->nullable();
            $table->json('undertime_dates')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_records');
    }
};
