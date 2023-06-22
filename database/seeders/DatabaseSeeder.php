<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Equipment;
use App\Models\EquipmentType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        EquipmentType::factory()->times(10)->create();
        Equipment::factory()->times(30)->create();
    }
}
