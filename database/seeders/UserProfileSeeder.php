<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user profile
        $adminUser = User::where('email', 'admin@darul-arqam.com')->first();
        if ($adminUser) {
            UserProfile::create([
                'user_id' => $adminUser->id,
                'first_name' => 'Admin',
                'last_name' => 'User',
                'gender' => 'male',
                'phone' => '+234-800-0000-001',
                'address' => 'Darul Arqam School, Headquarters',
                'date_of_birth' => '1980-01-15',
                'blood_group' => 'O+',
                'nationality' => 'Nigerian',
                'state_of_origin' => 'Lagos',
                'occupation' => 'Administrator',
                'qualification' => 'Bachelor of Science',
                'specialization' => 'School Management',
                'employment_date' => '2020-01-01',
                'status' => 'active',
            ]);
        }

        // Teachers profiles
        $teachers = [
            ['email' => 'ahmed.ali@darul-arqam.com', 'first_name' => 'Ahmed', 'last_name' => 'Ali', 'specialization' => 'Mathematics', 'subjects' => ['Mathematics', 'Basic Science'], 'classes' => []],
            ['email' => 'fatima.hassan@darul-arqam.com', 'first_name' => 'Fatima', 'last_name' => 'Hassan', 'specialization' => 'English Language', 'subjects' => ['English Language', 'Social Studies'], 'classes' => []],
            ['email' => 'mohamed.karim@darul-arqam.com', 'first_name' => 'Mohamed', 'last_name' => 'Karim', 'specialization' => 'Science', 'subjects' => ['Basic Science', 'Computer Science'], 'classes' => []],
            ['email' => 'sarah.ibrahim@darul-arqam.com', 'first_name' => 'Sarah', 'last_name' => 'Ibrahim', 'specialization' => 'Islamic Studies', 'subjects' => ['Islamic Studies', 'Arabic', 'Qur\'an'], 'classes' => []],
        ];

        // Get school classes for assignment
        $schoolClasses = \App\Models\SchoolClass::active()->orderBy('name')->get();
        $classIndex = 0;

        foreach ($teachers as $teacherData) {
            $user = User::where('email', $teacherData['email'])->first();
            if ($user && !$user->profile) {
                // Assign 1-2 classes to each teacher
                $assignedClasses = [];
                if ($classIndex < count($schoolClasses)) {
                    $assignedClasses[] = $schoolClasses[$classIndex]->name . ' - ' . $schoolClasses[$classIndex]->class_code;
                }
                if ($classIndex + 1 < count($schoolClasses)) {
                    $assignedClasses[] = $schoolClasses[$classIndex + 1]->name . ' - ' . $schoolClasses[$classIndex + 1]->class_code;
                }

                UserProfile::create([
                    'user_id' => $user->id,
                    'first_name' => $teacherData['first_name'],
                    'last_name' => $teacherData['last_name'],
                    'gender' => $teacherData['first_name'] === 'Fatima' || $teacherData['first_name'] === 'Sarah' ? 'female' : 'male',
                    'phone' => '+234-800-0000-' . str_pad($user->id, 3, '0', STR_PAD_LEFT),
                    'address' => 'Darul Arqam Staff Quarters',
                    'date_of_birth' => '1985-06-10',
                    'blood_group' => 'A+',
                    'nationality' => 'Nigerian',
                    'state_of_origin' => 'Lagos',
                    'occupation' => 'Teacher',
                    'qualification' => 'Bachelor of Education',
                    'specialization' => $teacherData['specialization'],
                    'employment_date' => '2019-09-01',
                    'subjects' => $teacherData['subjects'],
                    'classes' => $assignedClasses,
                    'date_joined' => '2019-09-01',
                    'status' => 'active',
                ]);
                $classIndex += 2;
            }
        }

        // Students profiles
        $students = [
            ['email' => 'hassan.m@darul-arqam.com', 'first_name' => 'Hassan', 'last_name' => 'Mohammed', 'admission_number' => 'STU-2024-001'],
            ['email' => 'aisha.a@darul-arqam.com', 'first_name' => 'Aisha', 'last_name' => 'Ahmed', 'admission_number' => 'STU-2024-002'],
            ['email' => 'omar.i@darul-arqam.com', 'first_name' => 'Omar', 'last_name' => 'Ibrahim', 'admission_number' => 'STU-2024-003'],
            ['email' => 'layla.h@darul-arqam.com', 'first_name' => 'Layla', 'last_name' => 'Hassan', 'admission_number' => 'STU-2024-004'],
            ['email' => 'khalid.a@darul-arqam.com', 'first_name' => 'Khalid', 'last_name' => 'Ahmed', 'admission_number' => 'STU-2024-005'],
            ['email' => 'noor.a@darul-arqam.com', 'first_name' => 'Noor', 'last_name' => 'Ali', 'admission_number' => 'STU-2024-006'],
        ];

        $schoolClasses = \App\Models\SchoolClass::pluck('id')->toArray();
        $classIndex = 0;

        foreach ($students as $studentData) {
            $user = User::where('email', $studentData['email'])->first();
            if ($user && !$user->profile) {
                UserProfile::create([
                    'user_id' => $user->id,
                    'first_name' => $studentData['first_name'],
                    'last_name' => $studentData['last_name'],
                    'admission_number' => $studentData['admission_number'],
                    'gender' => in_array($studentData['first_name'], ['Aisha', 'Layla', 'Noor']) ? 'female' : 'male',
                    'phone' => '+234-800-1000-' . str_pad($user->id, 3, '0', STR_PAD_LEFT),
                    'address' => 'Various Student Addresses',
                    'date_of_birth' => '2010-03-20',
                    'blood_group' => 'B+',
                    'nationality' => 'Nigerian',
                    'state_of_origin' => 'Lagos',
                    'school_class_id' => $schoolClasses[$classIndex % count($schoolClasses)] ?? null,
                    'admission_date' => '2024-09-01',
                    'previous_school' => 'Primary School',
                    'medical_conditions' => null,
                    'status' => 'active',
                ]);
                $classIndex++;
            }
        }

        // Parents profiles
        $parents = [
            ['email' => 'dr.mohammed@darul-arqam.com', 'first_name' => 'Dr. Mohammed', 'last_name' => 'Hassan', 'occupation' => 'Medical Doctor'],
            ['email' => 'amina.i@darul-arqam.com', 'first_name' => 'Amina', 'last_name' => 'Ibrahim', 'occupation' => 'Business Owner'],
            ['email' => 'abdullah.a@darul-arqam.com', 'first_name' => 'Abdullah', 'last_name' => 'Ahmed', 'occupation' => 'Engineer'],
        ];

        foreach ($parents as $parentData) {
            $user = User::where('email', $parentData['email'])->first();
            if ($user && !$user->profile) {
                UserProfile::create([
                    'user_id' => $user->id,
                    'first_name' => $parentData['first_name'],
                    'last_name' => $parentData['last_name'],
                    'gender' => $parentData['first_name'] === 'Amina' ? 'female' : 'male',
                    'phone' => '+234-800-2000-' . str_pad($user->id, 3, '0', STR_PAD_LEFT),
                    'address' => 'Parent/Guardian Address',
                    'date_of_birth' => '1970-05-12',
                    'blood_group' => 'AB+',
                    'nationality' => 'Nigerian',
                    'state_of_origin' => 'Lagos',
                    'occupation' => $parentData['occupation'],
                    'qualification' => 'Tertiary Education',
                    'status' => 'active',
                ]);
            }
        }

        $this->command->info('User profiles seeded successfully!');
    }
}
