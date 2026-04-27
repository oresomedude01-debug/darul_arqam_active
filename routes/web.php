<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherClassController;
use App\Http\Controllers\TeacherResultController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ClassSubjectController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SchoolSettingsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\FeeItemController;
use App\Http\Controllers\FeeStructureController;
use App\Http\Controllers\ResultsController;
use App\Http\Controllers\StudentPortalController;
use App\Http\Controllers\ParentPortalController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\TeacherDashboardController;
use App\Http\Controllers\ParentManagementController;
use App\Http\Controllers\GenericDashboardController;
use App\Http\Controllers\Admin\RBACController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\MailTestController;
use App\Http\Controllers\ProfileController;

// Landing Page (Public)
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Public Pages
Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/gallery', function () {
    return view('gallery');
})->name('gallery');

Route::get('/blog', function () {
    return view('blog.index');
})->name('blog.index');

// Language/Localization Routes
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');
Route::get('/api/locale/current', [LocaleController::class, 'getCurrent'])->name('locale.current');

// Authentication Routes
require __DIR__ . '/auth.php';

// Paystack Callback (Public - No Auth Required)
Route::post('/payments/paystack/callback', [PaymentController::class, 'handlePaystackCallback'])->name('paystack.callback');

// Password Reset Route (Public - No Auth Required)
Route::post('/api/password-reset-request', [PasswordResetController::class, 'sendResetLink'])->name('password.reset.request');


Route::middleware(['auth', 'role.redirect'])->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'changePasswordForm'])->name('profile.change-password');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password.store');

    // Notification Routes
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/{id}/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');

    // Dashboard Routes - Role-based
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->hasRole('Administrator')) {
            return app(AdminDashboardController::class)->index();
        } elseif ($user->hasRole('teacher')) {
            return app(TeacherDashboardController::class)->index();
        } elseif ($user->hasRole('student')) {
            return redirect()->route('student-portal.dashboard');
        } elseif ($user->hasRole('parent')) {
            return redirect()->route('parent-portal.dashboard');
        } else {
            return app(GenericDashboardController::class)->index();
        }
    })->name('dashboard');

    // Admin-only Dashboard
    Route::middleware('role:admin')->get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Teacher-only Dashboard
    Route::middleware('role:teacher')->get('/teacher/dashboard', [TeacherDashboardController::class, 'index'])->name('teacher.dashboard');

    // Generic Dashboard for other roles
    Route::middleware('role:!admin,!teacher,!student,!parent')->get('/dashboard/generic', [GenericDashboardController::class, 'index'])->name('dashboard.generic');

    // Students Management - Accessible to: Admin, Teachers
    Route::middleware('role:admin,teacher')->group(function () {
        Route::resource('students', StudentController::class);
        Route::put('/students/{id}/update-status', [StudentController::class, 'updateStatus'])->name('students.update-status');
        Route::get('/students/{id}/print', [StudentController::class, 'print'])->name('students.print');
        Route::get('/students-export', [StudentController::class, 'export'])->name('students.export');
        Route::get('/students-import', [StudentController::class, 'importForm'])->name('students.import-form');
        Route::post('/students-import', [StudentController::class, 'import'])->name('students.import');
        Route::get('/students-template', [StudentController::class, 'downloadTemplate'])->name('students.template');
    });

    // Teacher Class Students - Accessible to: Teachers only (view-only)
    Route::middleware('role:teacher')->group(function () {
        Route::get('/teacher/my-classes', [TeacherClassController::class, 'viewMyClasses'])->name('teacher.my-classes');
        Route::get('/teacher/class/students', [TeacherClassController::class, 'viewStudents'])->name('teacher.class.students');
        Route::get('/teacher/class/students/{student}', [TeacherClassController::class, 'viewStudent'])->name('teacher.student-detail');
        Route::get('/teacher/class/students/export', [TeacherClassController::class, 'exportStudents'])->name('teacher.class.export');
        Route::get('/teacher/class/attendance', [TeacherClassController::class, 'viewAttendance'])->name('teacher.class.attendance');
        Route::get('/teacher/class/mark-attendance', [TeacherClassController::class, 'markAttendance'])->name('teacher.mark-attendance');
        Route::post('/teacher/class/mark-attendance', [TeacherClassController::class, 'storeAttendance'])->name('teacher.store-attendance');
        Route::get('/teacher/classes-timetable', [TeacherClassController::class, 'viewClassesTimetable'])->name('teacher.classes-timetable');
        Route::get('/teacher/my-timetable', [TeacherClassController::class, 'viewPersonalTimetable'])->name('teacher.my-timetable');

        // Teacher Results - Permission-based access
        Route::get('/teacher/results/classes', [TeacherResultController::class, 'viewMyClasses'])->name('teacher.results.classes');
        Route::get('/teacher/results/class-results', [TeacherResultController::class, 'viewClassResults'])->name('teacher.results.view');
        Route::get('/teacher/results/{id}/edit', [TeacherResultController::class, 'editResult'])->name('teacher.results.edit');
        Route::put('/teacher/results/{id}', [TeacherResultController::class, 'updateResult'])->name('teacher.results.update');
        Route::get('/teacher/results/batch-edit', [TeacherResultController::class, 'batchEditResults'])->name('teacher.results.batch-edit');
        Route::post('/teacher/results/batch-store', [TeacherResultController::class, 'storeBatchResults'])->name('teacher.results.batch-store');
        Route::post('/teacher/results/release', [TeacherResultController::class, 'releaseResults'])->name('teacher.results.release');
        Route::get('/teacher/results/previous', [TeacherResultController::class, 'viewPreviousResults'])->name('teacher.results.previous');
    });

    // Teachers Management - Accessible to: Admin only
    Route::middleware('role:admin')->group(function () {
        Route::resource('teachers', TeacherController::class);
        Route::get('/teachers-export', [TeacherController::class, 'exportCsv'])->name('teachers.export');
        Route::get('/teachers/{teacher}/assign', [TeacherController::class, 'assign'])->name('teachers.assign');
        Route::post('/teachers/{teacher}/assign', [TeacherController::class, 'updateAssignments'])->name('teachers.update-assignments');
    });

    // Classes Management - Accessible to: Admin, Teachers
    Route::middleware('role:admin,teacher')->group(function () {
        Route::resource('classes', ClassController::class);
        Route::get('/classes-export', [ClassController::class, 'exportCsv'])->name('classes.export');
        Route::post('/classes/{class}/enroll-students', [ClassController::class, 'enrollStudents'])->name('classes.enroll-students');
        Route::post('/classes/{class}/move-students', [ClassController::class, 'moveStudents'])->name('classes.move-students');

        // Class-Subject Assignment Management
        Route::get('/classes/{class}/subjects', [ClassSubjectController::class, 'index'])->name('classes.subjects.index');
        Route::post('/classes/{class}/subjects', [ClassSubjectController::class, 'store'])->name('classes.subjects.store');
        Route::put('/classes/{class}/subjects/{subject}', [ClassSubjectController::class, 'update'])->name('classes.subjects.update');
        Route::delete('/classes/{class}/subjects/{subject}', [ClassSubjectController::class, 'destroy'])->name('classes.subjects.destroy');

        // Timetable Management
        Route::get('/timetable', [TimetableController::class, 'view'])->name('timetable.view');
        Route::get('/classes/{class}/timetable', [TimetableController::class, 'index'])->name('classes.timetable.index');
        Route::post('/classes/{class}/timetable', [TimetableController::class, 'store'])->name('classes.timetable.store');
        Route::post('/classes/{class}/timetable/bulk', [TimetableController::class, 'bulkStore'])->name('classes.timetable.bulk-store');
        Route::put('/classes/{class}/timetable/{timetable}', [TimetableController::class, 'update'])->name('classes.timetable.update');
        Route::delete('/classes/{class}/timetable/{timetable}', [TimetableController::class, 'destroy'])->name('classes.timetable.destroy');
    });

    // Subjects Management - Accessible to: Admin, Teachers
    Route::middleware('role:admin,teacher')->group(function () {
        Route::resource('subjects', SubjectController::class);
        Route::post('/subjects/{subject}/assign-classes', [SubjectController::class, 'assignClasses'])->name('subjects.assign-classes');
    });

    // Attendance Management - Accessible to: Admin, Teachers
    Route::middleware('role:admin,teacher')->group(function () {
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
        Route::post('/attendance/store', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::get('/attendance/records', [AttendanceController::class, 'records'])->name('attendance.records');
        Route::get('/attendance/student/{student}', [AttendanceController::class, 'studentProfile'])->name('attendance.student-profile');
        Route::post('/attendance/mark-all-present', [AttendanceController::class, 'markAllPresent'])->name('attendance.mark-all-present');
    });

    // School Settings Management - Accessible to: Admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('/settings', [SchoolSettingsController::class, 'index'])->name('settings.index');

        Route::prefix('settings/school')->name('settings.school.')->group(function () {
            Route::get('/', [SchoolSettingsController::class, 'index'])->name('index');
            Route::get('/general/edit', [SchoolSettingsController::class, 'editGeneral'])->name('edit-general');
            Route::put('/general', [SchoolSettingsController::class, 'updateGeneral'])->name('update-general');
            Route::get('/academic/edit', [SchoolSettingsController::class, 'editAcademic'])->name('edit-academic');
            Route::put('/academic', [SchoolSettingsController::class, 'updateAcademic'])->name('update-academic');

            // Academic Sessions Management
            Route::prefix('academic')->name('academic.')->group(function () {
                Route::get('/sessions', [SchoolSettingsController::class, 'academicSessions'])->name('sessions');
                Route::get('/sessions/create', [SchoolSettingsController::class, 'createAcademicSession'])->name('create');
                Route::post('/sessions', [SchoolSettingsController::class, 'storeAcademicSession'])->name('store');
                Route::get('/sessions/{academicSession}/edit', [SchoolSettingsController::class, 'editAcademicSession'])->name('edit');
                Route::put('/sessions/{academicSession}', [SchoolSettingsController::class, 'updateAcademicSession'])->name('update');
                Route::post('/sessions/{academicSession}/set-current', [SchoolSettingsController::class, 'setCurrentSession'])->name('set-current');
                Route::delete('/sessions/{academicSession}', [SchoolSettingsController::class, 'deleteAcademicSession'])->name('delete');
            });

            Route::get('/school-days/edit', [SchoolSettingsController::class, 'editSchoolDays'])->name('edit-school-days');
            Route::put('/school-days', [SchoolSettingsController::class, 'updateSchoolDays'])->name('update-school-days');
            Route::get('/grading/edit', [SchoolSettingsController::class, 'editGrading'])->name('edit-grading');
            Route::put('/grading', [SchoolSettingsController::class, 'updateGrading'])->name('update-grading');
            Route::get('/promotion/edit', [SchoolSettingsController::class, 'editPromotion'])->name('edit-promotion');
            Route::put('/promotion', [SchoolSettingsController::class, 'updatePromotion'])->name('update-promotion');
            Route::get('/preferences/edit', [SchoolSettingsController::class, 'editPreferences'])->name('edit-preferences');
            Route::put('/preferences', [SchoolSettingsController::class, 'updatePreferences'])->name('update-preferences');
            Route::get('/currency/edit', [SchoolSettingsController::class, 'editCurrency'])->name('edit-currency');
            Route::put('/currency', [SchoolSettingsController::class, 'updateCurrency'])->name('update-currency');
            Route::get('/financial/edit', [SchoolSettingsController::class, 'editFinancial'])->name('edit-financial');
            Route::put('/financial', [SchoolSettingsController::class, 'updateFinancial'])->name('update-financial');
            Route::get('/paystack/edit', [SchoolSettingsController::class, 'editPaystack'])->name('edit-paystack');
            Route::put('/paystack', [SchoolSettingsController::class, 'updatePaystack'])->name('update-paystack');
        });
    });
    // Payment Routes - Admin only
    Route::middleware('role:admin')->prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');

        // Fee Items
        Route::prefix('fee-items')->name('fee-items.')->group(function () {
            Route::post('/{feeStructure}/add', [PaymentController::class, 'addFeeItem'])->name('add');
            Route::put('/{feeItem}', [PaymentController::class, 'updateFeeItem'])->name('update');
            Route::delete('/{feeItem}', [PaymentController::class, 'deleteFeeItem'])->name('delete');
        });

        // Student Bills
        Route::prefix('bills')->name('bills.')->group(function () {
            Route::get('/', [PaymentController::class, 'studentBills'])->name('index');
            Route::get('/{bill}', [PaymentController::class, 'viewBill'])->name('view');
            Route::post('/{bill}/add-payment', [PaymentController::class, 'recordPayment'])->name('add-payment');
            Route::post('/{bill}/add-discount', [PaymentController::class, 'addDiscount'])->name('add-discount');
            Route::get('/{bill}/print-invoice', [PaymentController::class, 'printInvoice'])->name('print-invoice');
            Route::post('/generate', [PaymentController::class, 'generateBills'])->name('generate');
            // Paystack Payment Routes
            Route::get('/{bill}/paystack', [PaymentController::class, 'showPaystackForm'])->name('paystack-form');
            Route::post('/{bill}/paystack/initialize', [PaymentController::class, 'initializePaystackPayment'])->name('paystack-initialize');
        });

        // Payment Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/payment-history', [PaymentController::class, 'paymentHistory'])->name('payment-history');
            Route::get('/debt-management', [PaymentController::class, 'debtManagement'])->name('debt-management');
        });

        // Payment Receipts
        Route::prefix('receipts')->name('receipts.')->group(function () {
            Route::post('/{payment}/generate', [PaymentController::class, 'generateReceipt'])->name('generate');
            Route::get('/', [PaymentController::class, 'receiptsList'])->name('list');
            Route::get('/{receipt}/view', [PaymentController::class, 'viewReceipt'])->name('view');
            Route::get('/{receipt}/print', [PaymentController::class, 'printReceipt'])->name('print');
            Route::get('/{receipt}/pdf', [PaymentController::class, 'downloadReceiptPDF'])->name('pdf');
        });

        // Direct routes for reports (for easier navigation)
        Route::get('/payment-history', [PaymentController::class, 'paymentHistory'])->name('payment-history');
        Route::get('/debt-management', [PaymentController::class, 'debtManagement'])->name('debt-management');
        Route::post('/send-payment-reminders', [\App\Http\Controllers\BillingController::class, 'sendPaymentReminders'])->name('send-payment-reminders');
        Route::get('/fee-items', [PaymentController::class, 'manageFeeItemsIndex'])->name('fee-items.index');
    });

    // NEW: Billing System Routes (Admin only)
    Route::middleware('role:admin')->prefix('billing')->name('billing.')->group(function () {
        // Fee Items CRUD
        Route::resource('fee-items', \App\Http\Controllers\FeeItemController::class, [
            'only' => ['index', 'create', 'store', 'edit', 'update', 'destroy']
        ]);
        Route::post('fee-items/{feeItem}/activate', [\App\Http\Controllers\FeeItemController::class, 'activate'])->name('fee-items.activate');
        Route::post('fee-items/{feeItem}/deactivate', [\App\Http\Controllers\FeeItemController::class, 'deactivate'])->name('fee-items.deactivate');

        // Fee Structures (Session + Class pricing)
        Route::resource('fee-structures', \App\Http\Controllers\FeeStructureController::class, [
            'only' => ['index', 'create', 'store', 'edit', 'update', 'destroy']
        ]);
        Route::get('fee-structures/{session}/{class}/summary', [\App\Http\Controllers\FeeStructureController::class, 'summary'])->name('fee-structures.summary');

        // Bill Management - using BillingController
        Route::get('/generate-bills', [\App\Http\Controllers\BillingController::class, 'generateBillsForm'])->name('generate-bills.form');
        Route::post('/generate-bills', [\App\Http\Controllers\BillingController::class, 'generateBills'])->name('generate-bills');
        Route::post('/generate-individual-bill', [\App\Http\Controllers\BillingController::class, 'generateIndividualBill'])->name('generate-individual-bill');
        Route::get('/student-bills/{student}', [\App\Http\Controllers\BillingController::class, 'studentBills'])->name('student-bills');
        Route::get('/bill/{bill}', [\App\Http\Controllers\BillingController::class, 'viewBill'])->name('bill-view');
        Route::post('/bill/{bill}/record-payment', [\App\Http\Controllers\BillingController::class, 'recordPayment'])->name('record-payment');
        Route::post('/bill/{bill}/apply-discount', [\App\Http\Controllers\BillingController::class, 'applyDiscount'])->name('apply-discount');
        Route::post('/payment/{payment}/generate-receipt', [\App\Http\Controllers\BillingController::class, 'generateReceipt'])->name('generate-receipt');
        Route::get('/receipt/{receipt}', [\App\Http\Controllers\BillingController::class, 'viewReceipt'])->name('receipt-view');
        Route::get('/receipt/{receipt}/print', [\App\Http\Controllers\BillingController::class, 'printReceipt'])->name('receipt-print');
        Route::get('/payment-history', [\App\Http\Controllers\BillingController::class, 'paymentHistory'])->name('payment-history');
        Route::get('/debt-management', [\App\Http\Controllers\BillingController::class, 'debtManagement'])->name('debt-management');
    });



    // Grades Management - Accessible to: Admin, Teachers
    Route::middleware('role:admin,teacher')->group(function () {
        Route::get('/grades', [GradeController::class, 'index'])->name('grades.index');
        Route::post('/grades/store', [GradeController::class, 'store'])->name('grades.store');
        Route::get('/grades/class-results', [GradeController::class, 'classResults'])->name('grades.class-results');
        Route::get('/grades/student/{student}', [GradeController::class, 'studentProfile'])->name('grades.student-profile');

        // Grade Scales Management
        Route::get('/grades/scales', [GradeController::class, 'gradeScales'])->name('grades.scales');
        Route::post('/grades/scales', [GradeController::class, 'storeGradeScale'])->name('grades.scales.store');
        Route::put('/grades/scales/{gradeScale}', [GradeController::class, 'updateGradeScale'])->name('grades.scales.update');
        Route::delete('/grades/scales/{gradeScale}', [GradeController::class, 'destroyGradeScale'])->name('grades.scales.destroy');

        // Exam Types Management
        Route::get('/grades/exam-types', [GradeController::class, 'examTypes'])->name('grades.exam-types');
        Route::post('/grades/exam-types', [GradeController::class, 'storeExamType'])->name('grades.exam-types.store');
        Route::put('/grades/exam-types/{examType}', [GradeController::class, 'updateExamType'])->name('grades.exam-types.update');
        Route::delete('/grades/exam-types/{examType}', [GradeController::class, 'destroyExamType'])->name('grades.exam-types.destroy');
    });

    // Academic Calendar & Events Management - Accessible to: Admin only for create/edit/delete
    Route::middleware('role:admin,teacher')->group(function () {
        Route::get('/calendar', [EventController::class, 'index'])->name('calendar.index');
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
        Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    });

    // Registration Tokens Management - Accessible to: Admin only
    Route::middleware('role:admin')->group(function () {
        Route::resource('tokens', TokenController::class);
        Route::post('/tokens/bulk-disable', [TokenController::class, 'bulkDisable'])->name('tokens.bulk-disable');
        Route::post('/tokens/bulk-enable', [TokenController::class, 'bulkEnable'])->name('tokens.bulk-enable');
        Route::post('/tokens/validate', [TokenController::class, 'validate'])->name('tokens.validate');
    });

    // User Management - Accessible to: Admin only
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/assign-roles', [UserController::class, 'assignRoles'])->name('users.assign-roles');
    });

    // Role Management - Accessible to: Admin only
    Route::middleware('role:admin')->group(function () {
        Route::resource('roles', RoleController::class);
    });



    // Billing Module - Modern Fee & Payment Management
    Route::prefix('billing')->name('billing.')->group(function () {
        // Bill Generation
        Route::post('/generate-bills', [\App\Http\Controllers\BillingController::class, 'generateBills'])->name('generate-bills');
        Route::post('/generate-individual-bill', [\App\Http\Controllers\BillingController::class, 'generateIndividualBill'])->name('generate-individual-bill');

        // Student Bills
        Route::get('/student-bills/{student}', [\App\Http\Controllers\BillingController::class, 'studentBills'])->name('student-bills');
        Route::get('/bill/{bill}', [\App\Http\Controllers\BillingController::class, 'viewBill'])->name('bill-view');

        // Payments
        Route::post('/bill/{bill}/record-payment', [\App\Http\Controllers\BillingController::class, 'recordPayment'])->name('record-payment');
        Route::post('/bill/{bill}/apply-discount', [\App\Http\Controllers\BillingController::class, 'applyDiscount'])->name('apply-discount');

        // Receipts
        Route::post('/payment/{payment}/generate-receipt', [\App\Http\Controllers\BillingController::class, 'generateReceipt'])->name('generate-receipt');
        Route::get('/receipt/{receipt}', [\App\Http\Controllers\BillingController::class, 'viewReceipt'])->name('receipt-view');
        Route::get('/receipt/{receipt}/print', [\App\Http\Controllers\BillingController::class, 'printReceipt'])->name('receipt-print');

        // Reports
        Route::get('/payment-history', [\App\Http\Controllers\BillingController::class, 'paymentHistory'])->name('payment-history');
        Route::get('/debt-management', [\App\Http\Controllers\BillingController::class, 'debtManagement'])->name('debt-management');
        Route::post('/send-payment-reminders', [\App\Http\Controllers\BillingController::class, 'sendPaymentReminders'])->name('send-payment-reminders');
    });

    // Results Management
    Route::prefix('results')->name('results.')->group(function () {
        Route::get('/', [ResultsController::class, 'index'])->name('index');
        Route::get('/print-selection', [ResultsController::class, 'printSelection'])->name('print.selection');
        Route::get('/class-report-selection', [ResultsController::class, 'classReportSelection'])->name('class.report.selection');
        Route::get('/ajax/terms/{session}', [ResultsController::class, 'getTermsForSession'])->name('ajax.terms');
        Route::get('/ajax/classes/{term}', [ResultsController::class, 'getClassesForTerm'])->name('ajax.classes');
        Route::get('/ajax/students', [ResultsController::class, 'getStudentsForClass'])->name('ajax.students');
        Route::get('/session/{session}', [ResultsController::class, 'showSession'])->name('session.show');
        Route::post('/session/{session}/term/{term}/release', [ResultsController::class, 'releaseTermResults'])->name('term.release');
        Route::post('/session/{session}/term/{term}/recall', [ResultsController::class, 'recallTermResults'])->name('term.recall');
        Route::get('/session/{session}/term/{term}', [ResultsController::class, 'manageTerm'])->name('term.manage');
        Route::get('/session/{session}/term/{term}/class/{class}', [ResultsController::class, 'manageClass'])->name('class.manage');
        Route::post('/session/{session}/term/{term}/class/{class}/store', [ResultsController::class, 'storeClassResults'])->name('class.store');
        Route::get('/session/{session}/term/{term}/class/{class}/subject/{subject}', [ResultsController::class, 'viewSubjectResults'])->name('subject.view');
        Route::get('/session/{session}/term/{term}/class/{class}/report', [ResultsController::class, 'classReport'])->name('class.report');
        Route::get('/session/{session}/term/{term}/student/{student}/card', [ResultsController::class, 'studentCard'])->name('student.card');
        Route::post('/save-comments', [ResultsController::class, 'saveComments'])->name('save-comments');
        Route::get('/session/{session}/term/{term}/student/{student}/print', [ResultsController::class, 'printResultCard'])->name('student.print');
        Route::get('/session/{session}/term/{term}/summary', [ResultsController::class, 'termSummary'])->name('term.summary');
    });

    // Student Portal - Permission-based access (students only)
    // Student Portal - Accessible to: Students only
    Route::middleware(['auth', 'role:student'])->prefix('student-portal')->name('student-portal.')->group(function () {
        Route::get('/', [StudentPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/timetable', [StudentPortalController::class, 'timetable'])->name('timetable');
        Route::get('/calendar', [StudentPortalController::class, 'calendar'])->name('calendar');
        Route::get('/results', [StudentPortalController::class, 'results'])->name('results');
        Route::get('/attendance', [StudentPortalController::class, 'attendance'])->name('attendance');
        Route::get('/attendance/complaint', [StudentPortalController::class, 'fileAttendanceComplaint'])->name('attendance.complaint');
        Route::post('/attendance/complaint', [StudentPortalController::class, 'storeAttendanceComplaint'])->name('attendance.complaint.store');
        Route::get('/notifications', [StudentPortalController::class, 'notifications'])->name('notifications');
        Route::get('/profile', [StudentPortalController::class, 'profile'])->name('profile');
        Route::put('/profile', [StudentPortalController::class, 'updateProfile'])->name('profile.update');
    });

    // Parent Portal - Accessible to: Parents only
    Route::middleware(['auth', 'role:parent'])->prefix('parent-portal')->name('parent-portal.')->group(function () {
        Route::get('/', [App\Http\Controllers\ParentPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/children', [App\Http\Controllers\ParentPortalController::class, 'viewChildren'])->name('children');
        Route::get('/children/{childId}', [App\Http\Controllers\ParentPortalController::class, 'showChild'])->name('children.show');
        Route::get('/bills', [App\Http\Controllers\ParentPortalController::class, 'viewBills'])->name('bills');
        Route::get('/payment-history', [App\Http\Controllers\ParentPortalController::class, 'paymentHistory'])->name('payment-history');
        Route::get('/initiate-payment', [App\Http\Controllers\ParentPortalController::class, 'initiatePayment'])->name('initiate-payment');
        Route::post('/payment-methods', [App\Http\Controllers\ParentPortalController::class, 'selectPaymentMethod'])->name('payment-methods');
        Route::get('/attendance', [App\Http\Controllers\ParentPortalController::class, 'viewAttendance'])->name('attendance');
        Route::get('/results', [App\Http\Controllers\ParentPortalController::class, 'viewResults'])->name('results');
        Route::get('/results/terms/{sessionId}', [App\Http\Controllers\ParentPortalController::class, 'getTermsBySession'])->name('results.terms');
        Route::get('/results/{childId}/print', [App\Http\Controllers\ParentPortalController::class, 'printResults'])->name('results.print');
        Route::get('/results/session/{session}/term/{term}/student/{student}/print', [App\Http\Controllers\ParentPortalController::class, 'printResultsWithParams'])->name('results.print.params');
        Route::get('/announcements', [App\Http\Controllers\ParentPortalController::class, 'viewAnnouncements'])->name('announcements');
        Route::get('/calendar', [App\Http\Controllers\ParentPortalController::class, 'viewCalendar'])->name('calendar');
        Route::get('/performance', [App\Http\Controllers\ParentPortalController::class, 'viewPerformance'])->name('performance');
        Route::get('/documents', [App\Http\Controllers\ParentPortalController::class, 'viewDocuments'])->name('documents');
    });

    // Parent Portal Payment Routes - Accessible to: Parents only
    Route::middleware(['auth', 'role:parent'])->prefix('parent-payments')->name('parent-payments.')->group(function () {
        Route::post('/paystack', [PaymentController::class, 'parentInitiatePaystackPayment'])->name('paystack');
        Route::get('/paystack-callback', [PaymentController::class, 'parentPaystackCallback'])->name('paystack-callback');
        Route::post('/manual', [PaymentController::class, 'parentRecordManualPayment'])->name('manual');
    });

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RBACController::class, 'listRoles'])->name('index');
            Route::get('/create', [RBACController::class, 'createRole'])->name('create');
            Route::post('/', [RBACController::class, 'storeRole'])->name('store');
            Route::get('/{role}/edit', [RBACController::class, 'editRole'])->name('edit');
            Route::put('/{role}', [RBACController::class, 'updateRole'])->name('update');
            Route::delete('/{role}', [RBACController::class, 'deleteRole'])->name('destroy');
        });

        // Permissions Management
        Route::prefix('permissions')->name('permissions.')->group(function () {
            Route::get('/', [RBACController::class, 'listPermissions'])->name('index');
            Route::get('/create', [RBACController::class, 'createPermission'])->name('create');
            Route::post('/', [RBACController::class, 'storePermission'])->name('store');
            Route::get('/{permission}/edit', [RBACController::class, 'editPermission'])->name('edit');
            Route::put('/{permission}', [RBACController::class, 'updatePermission'])->name('update');
            Route::delete('/{permission}', [RBACController::class, 'deletePermission'])->name('destroy');
        });

        // User Roles Assignment
        Route::prefix('user-roles')->name('user-roles.')->group(function () {
            Route::get('/', [RBACController::class, 'userRoles'])->name('index');
            Route::get('/{user}/edit', [RBACController::class, 'editUserRoles'])->name('edit');
            Route::put('/{user}', [RBACController::class, 'updateUserRoles'])->name('update');
        });

        // Parent Management
        Route::prefix('parents')->name('parents.')->group(function () {
            Route::get('/', [ParentManagementController::class, 'index'])->name('index');
            Route::get('/create', [ParentManagementController::class, 'create'])->name('create');
            Route::post('/', [ParentManagementController::class, 'store'])->name('store');
            Route::get('/{parent}', [ParentManagementController::class, 'show'])->name('show');
            Route::get('/{parent}/edit', [ParentManagementController::class, 'edit'])->name('edit');
            Route::put('/{parent}', [ParentManagementController::class, 'update'])->name('update');
            Route::delete('/{parent}', [ParentManagementController::class, 'destroy'])->name('destroy');
            Route::post('/{parent}/assign-child', [ParentManagementController::class, 'assignChild'])->name('assign-child');
            Route::delete('/{parent}/unassign-child/{student}', [ParentManagementController::class, 'unassignChild'])->name('unassign-child');
            Route::get('/{parent}/children', [ParentManagementController::class, 'showChildren'])->name('children');
        });

        // Mail Configuration Testing
        Route::prefix('mail-test')->name('mail-test.')->group(function () {
            Route::get('/', [MailTestController::class, 'index'])->name('index');
            Route::post('/send', [MailTestController::class, 'sendTestEmail'])->name('send');
            Route::get('/config', [MailTestController::class, 'showConfig'])->name('config');
            Route::post('/connection', [MailTestController::class, 'testConnection'])->name('connection');
        });
    });
});

// Public Enrollment (No authentication required)
Route::prefix('enroll')->name('enrollment.')->group(function () {
    // Step 1: Token validation
    Route::get('/', [EnrollmentController::class, 'showTokenForm'])->name('token');
    Route::post('/validate-token', [EnrollmentController::class, 'validateToken'])->name('validate-token');

    // Step 2: Student details
    Route::get('/student-details', [EnrollmentController::class, 'showStep1'])->name('step1');
    Route::post('/student-details', [EnrollmentController::class, 'processStep1'])->name('process-step1');

    // Step 3: Previous school
    Route::get('/previous-school', [EnrollmentController::class, 'showStep2'])->name('step2');
    Route::post('/previous-school', [EnrollmentController::class, 'processStep2'])->name('process-step2');

    // Step 4: Health information
    Route::get('/health-information', [EnrollmentController::class, 'showStep3'])->name('step3');
    Route::post('/health-information', [EnrollmentController::class, 'processStep3'])->name('process-step3');

    // Step 5: Parent/Guardian
    Route::get('/parent-guardian', [EnrollmentController::class, 'showStep4'])->name('step4');
    Route::post('/parent-guardian', [EnrollmentController::class, 'processStep4'])->name('process-step4');

    // Success page
    Route::get('/success', [EnrollmentController::class, 'success'])->name('success');
});

