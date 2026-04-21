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
        Schema::table('leave_records', function (Blueprint $table) {
            $table->string('el_vl')->nullable()->change();
            $table->string('el_sl')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_records', function (Blueprint $table) {
            $table->decimal('el_vl', 8, 2)->nullable()->change();
            $table->decimal('el_sl', 8, 2)->nullable()->change();
        });
    }
};
