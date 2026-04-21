<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_records', function (Blueprint $table) {
            // Snapshot the employee's profile at the moment the record is created/saved.
            // These values are intentionally NOT updated when the user updates their profile.
            $table->string('snapshot_name')->nullable()->after('user_id')->comment('Full name at time of record creation');
            $table->string('snapshot_position')->nullable()->after('snapshot_name')->comment('Position at time of record creation');
            $table->string('snapshot_office')->nullable()->after('snapshot_position')->comment('Office at time of record creation');
        });
    }

    public function down(): void
    {
        Schema::table('leave_records', function (Blueprint $table) {
            $table->dropColumn(['snapshot_name', 'snapshot_position', 'snapshot_office']);
        });
    }
};
