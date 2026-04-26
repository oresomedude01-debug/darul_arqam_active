<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Don't use cache - just create the roles and permissions

        // Define all permissions grouped by category
        $permissions = [
            // Dashboard
            'dashboard' => [
                ['name' => 'View Dashboard', 'slug' => 'view-dashboard', 'group' => 'dashboard'],
            ],

            // Students
            'students' => [
                ['name' => 'View Students', 'slug' => 'view-students', 'group' => 'students'],
                ['name' => 'Create Student', 'slug' => 'create-student', 'group' => 'students'],
                ['name' => 'Edit Student', 'slug' => 'edit-student', 'group' => 'students'],
                ['name' => 'Delete Student', 'slug' => 'delete-student', 'group' => 'students'],
                ['name' => 'Export Students', 'slug' => 'export-students', 'group' => 'students'],
                ['name' => 'Import Students', 'slug' => 'import-students', 'group' => 'students'],
            ],

            // Teachers
            'teachers' => [
                ['name' => 'View Teachers', 'slug' => 'view-teachers', 'group' => 'teachers'],
                ['name' => 'Create Teacher', 'slug' => 'create-teacher', 'group' => 'teachers'],
                ['name' => 'Edit Teacher', 'slug' => 'edit-teacher', 'group' => 'teachers'],
                ['name' => 'Delete Teacher', 'slug' => 'delete-teacher', 'group' => 'teachers'],
                ['name' => 'Assign Teacher Classes', 'slug' => 'assign-teacher-classes', 'group' => 'teachers'],
            ],

            // Classes
            'classes' => [
                ['name' => 'View Classes', 'slug' => 'view-classes', 'group' => 'classes'],
                ['name' => 'Create Class', 'slug' => 'create-class', 'group' => 'classes'],
                ['name' => 'Edit Class', 'slug' => 'edit-class', 'group' => 'classes'],
                ['name' => 'Delete Class', 'slug' => 'delete-class', 'group' => 'classes'],
                ['name' => 'View Class Students', 'slug' => 'view-class-students', 'group' => 'classes'],
            ],

            // Subjects
            'subjects' => [
                ['name' => 'View Subjects', 'slug' => 'view-subjects', 'group' => 'subjects'],
                ['name' => 'Create Subject', 'slug' => 'create-subject', 'group' => 'subjects'],
                ['name' => 'Edit Subject', 'slug' => 'edit-subject', 'group' => 'subjects'],
                ['name' => 'Delete Subject', 'slug' => 'delete-subject', 'group' => 'subjects'],
            ],

            // Attendance
            'attendance' => [
                ['name' => 'View Attendance', 'slug' => 'view-attendance', 'group' => 'attendance'],
                ['name' => 'Create Attendance', 'slug' => 'create-attendance', 'group' => 'attendance'],
                ['name' => 'Edit Attendance', 'slug' => 'edit-attendance', 'group' => 'attendance'],
                ['name' => 'Delete Attendance', 'slug' => 'delete-attendance', 'group' => 'attendance'],
                ['name' => 'View Own Attendance', 'slug' => 'view-own-attendance', 'group' => 'attendance'],
            ],

            // Grades
            'grades' => [
                ['name' => 'View Grades', 'slug' => 'view-grades', 'group' => 'grades'],
                ['name' => 'Create Grade', 'slug' => 'create-grade', 'group' => 'grades'],
                ['name' => 'Edit Grade', 'slug' => 'edit-grade', 'group' => 'grades'],
                ['name' => 'Delete Grade', 'slug' => 'delete-grade', 'group' => 'grades'],
                ['name' => 'Manage Grade Scales', 'slug' => 'manage-grade-scales', 'group' => 'grades'],
                ['name' => 'Manage Exam Types', 'slug' => 'manage-exam-types', 'group' => 'grades'],
                ['name' => 'View Own Grades', 'slug' => 'view-own-grades', 'group' => 'grades'],
            ],

            // Timetable
            'timetable' => [
                ['name' => 'View Timetable', 'slug' => 'view-timetable', 'group' => 'timetable'],
                ['name' => 'Create Timetable', 'slug' => 'create-timetable', 'group' => 'timetable'],
                ['name' => 'Edit Timetable', 'slug' => 'edit-timetable', 'group' => 'timetable'],
                ['name' => 'Delete Timetable', 'slug' => 'delete-timetable', 'group' => 'timetable'],
                ['name' => 'View Classes Timetable', 'slug' => 'view-classes-timetable', 'group' => 'timetable'],
                ['name' => 'View Personal Timetable', 'slug' => 'view-personal-timetable', 'group' => 'timetable'],
            ],

            // Results - Teacher Result Management
            'results' => [
                ['name' => 'View Results', 'slug' => 'view-results', 'group' => 'results'],
                ['name' => 'View Class Results', 'slug' => 'view-class-results', 'group' => 'results'],
                ['name' => 'Edit Class Results', 'slug' => 'edit-class-results', 'group' => 'results'],
                ['name' => 'Release Class Results', 'slug' => 'release-class-results', 'group' => 'results'],
                ['name' => 'Create Result', 'slug' => 'create-result', 'group' => 'results'],
                ['name' => 'Edit Result', 'slug' => 'edit-result', 'group' => 'results'],
                ['name' => 'Delete Result', 'slug' => 'delete-result', 'group' => 'results'],
                ['name' => 'Approve Results', 'slug' => 'approve-results', 'group' => 'results'],
                ['name' => 'Write Teacher Comment', 'slug' => 'write-teacher-comment', 'group' => 'results'],
                ['name' => 'Write Head Teacher Comment', 'slug' => 'write-headteacher-comment', 'group' => 'results'],
                ['name' => 'View Own Results', 'slug' => 'view-own-results', 'group' => 'results'],
                ['name' => 'Manage School Results', 'slug' => 'manage-school-results', 'group' => 'results'],
            ],

            // Calendar & Events
            'calendar' => [
                ['name' => 'View Calendar', 'slug' => 'view-calendar', 'group' => 'calendar'],
                ['name' => 'Create Event', 'slug' => 'create-event', 'group' => 'calendar'],
                ['name' => 'Edit Event', 'slug' => 'edit-event', 'group' => 'calendar'],
                ['name' => 'Delete Event', 'slug' => 'delete-event', 'group' => 'calendar'],
                ['name' => 'Manage Academic Terms', 'slug' => 'manage-academic-terms', 'group' => 'calendar'],
            ],

            // Billing & Fees
            'billing' => [
                ['name' => 'View Bills', 'slug' => 'view-bills', 'group' => 'billing'],
                ['name' => 'Create Bill', 'slug' => 'create-bill', 'group' => 'billing'],
                ['name' => 'Generate Individual Bill', 'slug' => 'generate-individual-bill', 'group' => 'billing'],
                ['name' => 'Generate Bulk Bills', 'slug' => 'generate-bulk-bills', 'group' => 'billing'],
                ['name' => 'Edit Bill', 'slug' => 'edit-bill', 'group' => 'billing'],
                ['name' => 'Delete Bill', 'slug' => 'delete-bill', 'group' => 'billing'],
                ['name' => 'Manage Fee Items', 'slug' => 'manage-fee-items', 'group' => 'billing'],
                ['name' => 'Manage Fee Structures', 'slug' => 'manage-fee-structures', 'group' => 'billing'],
                ['name' => 'View Student Bills', 'slug' => 'view-student-bills', 'group' => 'billing'],
            ],

            // Payments
            'payments' => [
                ['name' => 'View Payments', 'slug' => 'view-payments', 'group' => 'payments'],
                ['name' => 'Record Payment', 'slug' => 'record-payment', 'group' => 'payments'],
                ['name' => 'Edit Payment', 'slug' => 'edit-payment', 'group' => 'payments'],
                ['name' => 'Delete Payment', 'slug' => 'delete-payment', 'group' => 'payments'],
                ['name' => 'Apply Discount', 'slug' => 'apply-discount', 'group' => 'payments'],
                ['name' => 'Process Paystack Payment', 'slug' => 'process-paystack-payment', 'group' => 'payments'],
                ['name' => 'View Payment History', 'slug' => 'view-payment-history', 'group' => 'payments'],
            ],

            // Receipts
            'receipts' => [
                ['name' => 'View Receipts', 'slug' => 'view-receipts', 'group' => 'receipts'],
                ['name' => 'Generate Receipt', 'slug' => 'generate-receipt', 'group' => 'receipts'],
                ['name' => 'Print Receipt', 'slug' => 'print-receipt', 'group' => 'receipts'],
                ['name' => 'Download Receipt PDF', 'slug' => 'download-receipt-pdf', 'group' => 'receipts'],
            ],

            // Debt Management
            'debt' => [
                ['name' => 'View Debt Management', 'slug' => 'view-debt-management', 'group' => 'debt'],
                ['name' => 'Manage Outstanding Debts', 'slug' => 'manage-outstanding-debts', 'group' => 'debt'],
                ['name' => 'View Debt Reports', 'slug' => 'view-debt-reports', 'group' => 'debt'],
            ],

            // Enrollment
            'enrollment' => [
                ['name' => 'View Enrollment', 'slug' => 'view-enrollment', 'group' => 'enrollment'],
                ['name' => 'Manage Registration Tokens', 'slug' => 'manage-registration-tokens', 'group' => 'enrollment'],
            ],

            // Parent Portal
            'parent_portal' => [
                ['name' => 'View Own Children', 'slug' => 'view-own-children', 'group' => 'parent_portal'],
                ['name' => 'View Own Bills', 'slug' => 'view-own-bills', 'group' => 'parent_portal'],
                ['name' => 'View Own Payment History', 'slug' => 'view-own-payment-history', 'group' => 'parent_portal'],
                ['name' => 'Make Payment via Paystack', 'slug' => 'make-paystack-payment', 'group' => 'parent_portal'],
                ['name' => 'Make Payment via Bank Transfer', 'slug' => 'make-bank-transfer-payment', 'group' => 'parent_portal'],
                ['name' => 'Make Payment via Cash', 'slug' => 'make-cash-payment', 'group' => 'parent_portal'],
                ['name' => 'Make Payment via Cheque', 'slug' => 'make-cheque-payment', 'group' => 'parent_portal'],
            ],

            // User Management
            'users' => [
                ['name' => 'View Users', 'slug' => 'view-users', 'group' => 'users'],
                ['name' => 'Create User', 'slug' => 'create-user', 'group' => 'users'],
                ['name' => 'Edit User', 'slug' => 'edit-user', 'group' => 'users'],
                ['name' => 'Delete User', 'slug' => 'delete-user', 'group' => 'users'],
                ['name' => 'Manage Parents', 'slug' => 'manage-parents', 'group' => 'users'],
                ['name' => 'Assign Roles', 'slug' => 'assign-roles', 'group' => 'users'],
                ['name' => 'Manage School Settings', 'slug' => 'manage-school-settings', 'group' => 'users'],
            ],

            // Role & Permission Management
            'roles' => [
                ['name' => 'View Roles', 'slug' => 'view-roles', 'group' => 'roles'],
                ['name' => 'Create Role', 'slug' => 'create-role', 'group' => 'roles'],
                ['name' => 'Edit Role', 'slug' => 'edit-role', 'group' => 'roles'],
                ['name' => 'Delete Role', 'slug' => 'delete-role', 'group' => 'roles'],
                ['name' => 'Manage Permissions', 'slug' => 'manage-permissions', 'group' => 'roles'],
            ],
        ];

        // Create all permissions
        foreach ($permissions as $category => $categoryPermissions) {
            foreach ($categoryPermissions as $permission) {
                Permission::firstOrCreate(
                    ['slug' => $permission['slug']],
                    [
                        'name' => $permission['name'],
                        'group' => $permission['group'],
                        'description' => $permission['name'] . ' in the system',
                    ]
                );
            }
        }

        // Create roles
        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Administrator',
                'description' => 'Full access to all features and settings',
                'is_active' => true,
            ]
        );

        $teacherRole = Role::firstOrCreate(
            ['slug' => 'teacher'],
            [
                'name' => 'Teacher',
                'description' => 'Can manage classes, attendance, and grades',
                'is_active' => true,
            ]
        );

        $studentRole = Role::firstOrCreate(
            ['slug' => 'student'],
            [
                'name' => 'Student',
                'description' => 'Can view own attendance, grades, and timetable',
                'is_active' => true,
            ]
        );

        $parentRole = Role::firstOrCreate(
            ['slug' => 'parent'],
            [
                'name' => 'Parent/Guardian',
                'description' => 'Can view child attendance and grades',
                'is_active' => true,
            ]
        );

        // Assign permissions to Admin role (all permissions)
        $adminPermissions = Permission::all();
        $adminRole->permissions()->sync($adminPermissions->pluck('id'));

        // Assign permissions to Teacher role
        $teacherPermissions = Permission::whereIn('slug', [
            'view-dashboard',
            'view-class-students',
            'view-attendance',
            'create-attendance',
            'edit-attendance',
            'view-grades',
            'edit-grade',
            'view-timetable',
            'view-classes-timetable',
            'view-personal-timetable',
            'view-calendar',
            'view-results',
            'view-class-results',
            'edit-class-results',
        ])->get();
        $teacherRole->permissions()->sync($teacherPermissions->pluck('id'));

        // Assign permissions to Student role
        $studentPermissions = Permission::whereIn('slug', [
            'view-dashboard',
            'view-own-attendance',
            'view-own-grades',
            'view-timetable',
            'view-calendar',
        ])->get();
        $studentRole->permissions()->sync($studentPermissions->pluck('id'));

        // Assign permissions to Parent role
        $parentPermissions = Permission::whereIn('slug', [
            'view-dashboard',
            'view-own-attendance',
            'view-own-grades',
            'view-timetable',
            'view-calendar',
            'view-own-children',
            'view-own-bills',
            'view-own-payment-history',
            'make-paystack-payment',
            'make-bank-transfer-payment',
            'make-cash-payment',
            'make-cheque-payment',
        ])->get();
        $parentRole->permissions()->sync($parentPermissions->pluck('id'));
    }
}
