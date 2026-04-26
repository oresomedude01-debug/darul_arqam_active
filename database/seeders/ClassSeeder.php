<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = [
            [
                'name' => 'Grade 1',
                'section' => 'A',
                'class_code' => 'G1-A',
                'capacity' => 40,
                'room_number' => '101',
                'status' => 'active',
                'description' => 'First Grade Section A',
            ],
            [
                'name' => 'Grade 1',
                'section' => 'B',
                'class_code' => 'G1-B',
                'capacity' => 38,
                'room_number' => '102',
                'status' => 'active',
                'description' => 'First Grade Section B',
            ],
            [
                'name' => 'Grade 2',
                'section' => 'A',
                'class_code' => 'G2-A',
                'capacity' => 40,
                'room_number' => '201',
                'status' => 'active',
                'description' => 'Second Grade Section A',
            ],
            [
                'name' => 'Grade 2',
                'section' => 'B',
                'class_code' => 'G2-B',
                'capacity' => 35,
                'room_number' => '202',
                'status' => 'active',
                'description' => 'Second Grade Section B',
            ],
            [
                'name' => 'Grade 3',
                'section' => 'A',
                'class_code' => 'G3-A',
                'capacity' => 42,
                'room_number' => '301',
                'status' => 'active',
                'description' => 'Third Grade Section A',
            ],
            [
                'name' => 'Grade 3',
                'section' => 'B',
                'class_code' => 'G3-B',
                'capacity' => 40,
                'room_number' => '302',
                'status' => 'active',
                'description' => 'Third Grade Section B',
            ],
        ];

        foreach ($classes as $class) {
            SchoolClass::create($class);
        }

        $this->command->info('Classes seeded successfully!');
    }
}
