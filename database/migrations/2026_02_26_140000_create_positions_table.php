<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Seed from existing config file if it exists
        $configPath = config_path('positions.php');
        if (file_exists($configPath)) {
            $positions = include $configPath;
            if (is_array($positions)) {
                foreach (array_filter($positions) as $name) {
                    \DB::table('positions')->insertOrIgnore(['name' => trim($name), 'created_at' => now(), 'updated_at' => now()]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
