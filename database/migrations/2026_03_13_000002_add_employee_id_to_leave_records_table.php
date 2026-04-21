<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_records', function (Blueprint $table) {
            // Allow user_id to be nullable (either user_id OR employee_id will be set)
            $table->unsignedBigInteger('employee_id')->nullable()->after('user_id');
            $table->foreign('employee_id')->references('id')->on('employees')->nullOnDelete();

            // Make user_id nullable so records can belong to standalone employees instead
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('leave_records', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropColumn('employee_id');
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};
