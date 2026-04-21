<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionsSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
           
        ];

        $now = now();
        foreach ($positions as $pos) {
            DB::table('positions')->updateOrInsert(
                ['name' => $pos['name']],
                ['code' => $pos['code'], 'updated_at' => $now, 'created_at' => $now]
            );
        }
    }
}
