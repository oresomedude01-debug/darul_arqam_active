<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            [
                'name' => 'Mathematics',
                'code' => 'MATH',
                'description' => 'Mathematical concepts and problem-solving',
                'is_active' => true,
            ],
            [
                'name' => 'English Language',
                'code' => 'ENG',
                'description' => 'English language and literature',
                'is_active' => true,
            ],
            [
                'name' => 'Arabic',
                'code' => 'ARAB',
                'description' => 'Arabic language studies',
                'is_active' => true,
            ],
            [
                'name' => 'Islamic Studies',
                'code' => 'ISLAM',
                'description' => 'Islamic teachings and practices',
                'is_active' => true,
            ],
            [
                'name' => 'Qur\'an',
                'code' => 'QURAN',
                'description' => 'Qur\'anic recitation and memorization',
                'is_active' => true,
            ],
            [
                'name' => 'Hadith',
                'code' => 'HADITH',
                'description' => 'Hadith studies and traditions',
                'is_active' => true,
            ],
            [
                'name' => 'Basic Science',
                'code' => 'SCIENCE',
                'description' => 'General science and natural phenomena',
                'is_active' => true,
            ],
            [
                'name' => 'Social Studies',
                'code' => 'SOCIAL',
                'description' => 'History, geography, and civics',
                'is_active' => true,
            ],
            [
                'name' => 'Computer Science',
                'code' => 'CS',
                'description' => 'Computer technology and programming',
                'is_active' => true,
            ],
            [
                'name' => 'Physical Education',
                'code' => 'PE',
                'description' => 'Sports and physical fitness',
                'is_active' => true,
            ],
            [
                'name' => 'Fine Arts',
                'code' => 'ART',
                'description' => 'Drawing, painting, and visual arts',
                'is_active' => true,
            ],
            [
                'name' => 'Music',
                'code' => 'MUSIC',
                'description' => 'Musical theory and performance',
                'is_active' => true,
            ],
        ];

        foreach ($subjects as $subject) {
            Subject::firstOrCreate(
                ['code' => $subject['code']],
                $subject
            );
        }
    }
}
