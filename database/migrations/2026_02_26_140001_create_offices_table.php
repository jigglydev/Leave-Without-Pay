<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Seed from existing config file if it exists
        $configPath = config_path('offices.php');
        if (file_exists($configPath)) {
            $offices = include $configPath;
            if (is_array($offices)) {
                foreach (array_filter($offices) as $name) {
                    \DB::table('offices')->insertOrIgnore(['name' => trim($name), 'created_at' => now(), 'updated_at' => now()]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};
