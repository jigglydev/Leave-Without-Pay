<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_number')->nullable()->unique()->after('position');
            $table->string('last_name')->nullable()->after('employee_number');
            $table->string('given_name')->nullable()->after('last_name');
            $table->string('middle_name')->nullable()->after('given_name');
            $table->string('suffix')->nullable()->after('middle_name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['employee_number', 'last_name', 'given_name', 'middle_name', 'suffix']);
        });
    }
};
