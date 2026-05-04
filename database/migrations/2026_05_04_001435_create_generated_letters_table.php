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
        Schema::create('generated_letters', function (Blueprint $table) {
            $table->id();
            $table->string('type');                     // 'tardy' or 'undertime'
            $table->string('person_id');                // 'user_X' or 'emp_X'
            $table->string('employee_name');            // snapshot
            $table->string('employee_prefix');
            $table->string('employee_position')->nullable();
            $table->string('employee_office')->nullable();
            $table->string('month');
            $table->string('year');
            $table->string('occurrences');             // tardiness_count / undertime_count
            $table->string('certifier_name')->nullable();
            $table->string('certifier_position')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_letters');
    }
};
