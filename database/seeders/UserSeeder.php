<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles
        $teacherRole = Role::where('slug', 'teacher')->first();
        $studentRole = Role::where('slug', 'student')->first();
        $parentRole = Role::where('slug', 'parent')->first();

        // Create Teachers
        $teachers = [
            ['name' => 'Ahmed Ali', 'email' => 'ahmed.ali@darul-arqam.com', 'role' => $teacherRole],
            ['name' => 'Fatima Hassan', 'email' => 'fatima.hassan@darul-arqam.com', 'role' => $teacherRole],
            ['name' => 'Mohamed Karim', 'email' => 'mohamed.karim@darul-arqam.com', 'role' => $teacherRole],
            ['name' => 'Sarah Ibrahim', 'email' => 'sarah.ibrahim@darul-arqam.com', 'role' => $teacherRole],
        ];

        foreach ($teachers as $teacherData) {
            $user = User::create([
                'name' => $teacherData['name'],
                'email' => $teacherData['email'],
                'password' => bcrypt('password123'),
            ]);
            if ($teacherData['role']) {
                $user->roles()->attach($teacherData['role']->id);
            }
        }

        $this->command->info('Teachers created: ' . count($teachers));

        // Create Students
        $students = [
            ['name' => 'Hassan Mohammed', 'email' => 'hassan.m@darul-arqam.com', 'role' => $studentRole],
            ['name' => 'Aisha Ahmed', 'email' => 'aisha.a@darul-arqam.com', 'role' => $studentRole],
            ['name' => 'Omar Ibrahim', 'email' => 'omar.i@darul-arqam.com', 'role' => $studentRole],
            ['name' => 'Layla Hassan', 'email' => 'layla.h@darul-arqam.com', 'role' => $studentRole],
            ['name' => 'Khalid Ahmed', 'email' => 'khalid.a@darul-arqam.com', 'role' => $studentRole],
            ['name' => 'Noor Ali', 'email' => 'noor.a@darul-arqam.com', 'role' => $studentRole],
        ];

        foreach ($students as $studentData) {
            $user = User::create([
                'name' => $studentData['name'],
                'email' => $studentData['email'],
                'password' => bcrypt('password123'),
            ]);
            if ($studentData['role']) {
                $user->roles()->attach($studentData['role']->id);
            }
        }

        $this->command->info('Students created: ' . count($students));

        // Create Parents
        $parents = [
            ['name' => 'Dr. Mohammed Hassan', 'email' => 'dr.mohammed@darul-arqam.com', 'role' => $parentRole],
            ['name' => 'Amina Ibrahim', 'email' => 'amina.i@darul-arqam.com', 'role' => $parentRole],
            ['name' => 'Abdullah Ahmed', 'email' => 'abdullah.a@darul-arqam.com', 'role' => $parentRole],
        ];

        foreach ($parents as $parentData) {
            $user = User::create([
                'name' => $parentData['name'],
                'email' => $parentData['email'],
                'password' => bcrypt('password123'),
            ]);
            if ($parentData['role']) {
                $user->roles()->attach($parentData['role']->id);
            }
        }

        $this->command->info('Parents created: ' . count($parents));
    }
}
