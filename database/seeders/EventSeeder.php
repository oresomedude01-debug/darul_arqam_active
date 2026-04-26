<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\AcademicTerm;
use App\Models\User;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first academic term and admin user
        $term = AcademicTerm::first();
        $admin = User::first();
        
        if (!$term || !$admin) {
            return;
        }

        $eventData = [
            [
                'title' => 'Term 1 Starts',
                'type' => 'celebration',
                'start_date' => Carbon::create(2024, 1, 8),
                'end_date' => Carbon::create(2024, 1, 8),
                'start_time' => '08:00:00',
                'end_time' => '16:00:00',
                'description' => 'First term of the academic year begins',
                'color' => '#10b981',
            ],
            [
                'title' => 'Mid-term Exams',
                'type' => 'exam',
                'start_date' => Carbon::create(2024, 2, 19),
                'end_date' => Carbon::create(2024, 2, 23),
                'start_time' => '09:00:00',
                'end_time' => '16:00:00',
                'description' => 'Mid-term examination for all classes',
                'color' => '#8b5cf6',
            ],
            [
                'title' => 'Winter Break',
                'type' => 'holiday',
                'start_date' => Carbon::create(2024, 3, 25),
                'end_date' => Carbon::create(2024, 3, 31),
                'start_time' => null,
                'end_time' => null,
                'description' => 'Winter holiday break for students',
                'color' => '#f59e0b',
            ],
            [
                'title' => 'Teacher Training Workshop',
                'type' => 'meeting',
                'start_date' => Carbon::create(2024, 4, 5),
                'end_date' => Carbon::create(2024, 4, 5),
                'start_time' => '09:00:00',
                'end_time' => '13:00:00',
                'description' => 'Professional development workshop for teachers',
                'color' => '#3b82f6',
            ],
            [
                'title' => 'Annual Sports Day',
                'type' => 'celebration',
                'start_date' => Carbon::create(2024, 4, 15),
                'end_date' => Carbon::create(2024, 4, 15),
                'start_time' => '08:00:00',
                'end_time' => '14:00:00',
                'description' => 'Annual sports competition and field day',
                'color' => '#06b6d4',
            ],
            [
                'title' => 'Term 1 Ends',
                'type' => 'break',
                'start_date' => Carbon::create(2024, 5, 10),
                'end_date' => Carbon::create(2024, 5, 10),
                'start_time' => '16:00:00',
                'end_time' => '16:00:00',
                'description' => 'End of first term',
                'color' => '#ef4444',
            ],
            [
                'title' => 'Summer Vacation',
                'type' => 'holiday',
                'start_date' => Carbon::create(2024, 5, 11),
                'end_date' => Carbon::create(2024, 8, 4),
                'start_time' => null,
                'end_time' => null,
                'description' => 'Long summer holiday break',
                'color' => '#f59e0b',
            ],
            [
                'title' => 'Term 2 Starts',
                'type' => 'celebration',
                'start_date' => Carbon::create(2024, 8, 5),
                'end_date' => Carbon::create(2024, 8, 5),
                'start_time' => '08:00:00',
                'end_time' => '16:00:00',
                'description' => 'Second term begins',
                'color' => '#10b981',
            ],
            [
                'title' => 'Parent-Teacher Conference',
                'type' => 'meeting',
                'start_date' => Carbon::create(2024, 9, 20),
                'end_date' => Carbon::create(2024, 9, 20),
                'start_time' => '14:00:00',
                'end_time' => '18:00:00',
                'description' => 'Parents meet with teachers for progress updates',
                'color' => '#3b82f6',
            ],
            [
                'title' => 'Final Exams',
                'type' => 'exam',
                'start_date' => Carbon::create(2024, 10, 28),
                'end_date' => Carbon::create(2024, 11, 8),
                'start_time' => '09:00:00',
                'end_time' => '16:00:00',
                'description' => 'Final examinations for the academic year',
                'color' => '#8b5cf6',
            ],
        ];

        foreach ($eventData as $data) {
            Event::create([
                'title' => $data['title'],
                'type' => $data['type'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'description' => $data['description'],
                'color' => $data['color'],
                'academic_term_id' => $term->id,
                'created_by' => $admin->id,
                'affected_classes' => json_encode([]),
            ]);
        }
    }
}
