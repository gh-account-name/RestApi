<?php

namespace Database\Factories;

use App\Models\EquipmentType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Equipment>
 */
class EquipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $equipmentType = EquipmentType::inRandomOrder()->first();

        return [
            "equipment_type_id" => $equipmentType->id,
            "serial_number" => $this->faker->unique()->regexify('[0-9A-Za-z-_@]{10}'),
            "note" => $this->faker->sentence,
        ];
    }
}
