<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\LeaveRecord;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fetch all existing records that do not yet have a snapshot
        $records = LeaveRecord::with('user')->whereNull('snapshot_name')->get();
        
        foreach ($records as $record) {
            if ($record->user) {
                // Ensure we use the proper update method or force save
                $record->update([
                    'snapshot_name'     => $record->user->full_name,
                    'snapshot_position' => $record->user->position,
                    'snapshot_office'   => $record->user->office,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op. To reverse, we'd set them back to null, but keeping them is fine.
    }
};
