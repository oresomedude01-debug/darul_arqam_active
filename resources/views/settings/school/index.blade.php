@extends('layouts.spa')

@section('title', 'School Settings')

@section('breadcrumb')
    <span class="text-gray-400">Settings</span>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">School Configuration</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">School Settings</h1>
            <p class="text-sm text-gray-600 mt-1">Configure school-wide settings that affect the entire system</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
        </a>
    </div>

    <!-- Alert for unsaved changes -->
    @if (session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Settings Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- 1. School Information Card -->
        <div class="card hover:shadow-lg transition-all duration-200">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white">
                        <i class="fas fa-school text-xl"></i>
                    </div>
                    <a href="{{ route('settings.school.edit-general') }}" class="btn btn-sm btn-outline">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">School Information</h3>
                <p class="text-sm text-gray-600 mb-4">School name, logo, contact details</p>
                
                <div class="space-y-2 text-sm">
                    <p><span class="font-semibold text-gray-700">Name:</span> <span class="text-gray-900">{{ $settings->school_name }}</span></p>
                    <p><span class="font-semibold text-gray-700">Email:</span> <span class="text-gray-900">{{ $settings->school_email ?? 'Not set' }}</span></p>
                    <p><span class="font-semibold text-gray-700">Phone:</span> <span class="text-gray-900">{{ $settings->school_phone ?? 'Not set' }}</span></p>
                </div>
            </div>
        </div>

        <!-- 2. Academic Session Card -->
        <div class="card hover:shadow-lg transition-all duration-200">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white">
                        <i class="fas fa-calendar-alt text-xl"></i>
                    </div>
                    <a href="{{ route('settings.school.academic.sessions') }}" class="btn btn-sm btn-outline">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Academic Session & Term</h3>
                <p class="text-sm text-gray-600 mb-4">Current session and term configuration</p>
                
                @php
                    $activeSession = \App\Models\AcademicSession::where('is_active', true)->first();
                    $activeTerm = $activeSession?->terms()->where('is_active', true)->first();
                @endphp
                
                <div class="space-y-2 text-sm">
                    @if ($activeSession)
                        <p><span class="font-semibold text-gray-700">Session:</span> <span class="badge badge-primary">{{ $activeSession->session }}</span></p>
                        @if ($activeTerm)
                            <p><span class="font-semibold text-gray-700">Term:</span> <span class="badge badge-info">{{ $activeTerm->name }}</span></p>
                            <p><span class="font-semibold text-gray-700">Start Date:</span> <span class="text-gray-900">{{ $activeTerm->start_date?->format('M d, Y') ?? 'Not set' }}</span></p>
                            <p><span class="font-semibold text-gray-700">End Date:</span> <span class="text-gray-900">{{ $activeTerm->end_date?->format('M d, Y') ?? 'Not set' }}</span></p>
                        @else
                            <p><span class="text-gray-600 italic">No active term set</span></p>
                        @endif
                    @else
                        <p><span class="text-gray-600 italic">No active session set</span></p>
                    @endif
                </div>
            </div>
        </div>

        <!-- 3. School Days Card -->
        <div class="card hover:shadow-lg transition-all duration-200">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center text-white">
                        <i class="fas fa-calendar-check text-xl"></i>
                    </div>
                    <a href="{{ route('settings.school.edit-school-days') }}" class="btn btn-sm btn-outline">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">School Operating Days</h3>
                <p class="text-sm text-gray-600 mb-4">Days when school is open</p>
                
                <div class="flex flex-wrap gap-2">
                    @foreach($settings->school_days ?? ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                        <span class="badge badge-success">{{ substr($day, 0, 3) }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- 4. Grading Settings Card -->
        <div class="card hover:shadow-lg transition-all duration-200">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center text-white">
                        <i class="fas fa-graduation-cap text-xl"></i>
                    </div>
                    <a href="{{ route('settings.school.edit-grading') }}" class="btn btn-sm btn-outline">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Grading & Assessment</h3>
                <p class="text-sm text-gray-600 mb-4">Grade boundaries and weightings</p>
                
                <div class="space-y-2 text-sm">
                    <p><span class="font-semibold text-gray-700">Pass Mark:</span> <span class="text-gray-900">{{ $settings->passing_score }}%</span></p>
                    <p><span class="font-semibold text-gray-700">CA Weight:</span> <span class="text-gray-900">{{ $settings->ca_weight }}%</span></p>
                    <p><span class="font-semibold text-gray-700">Exam Weight:</span> <span class="text-gray-900">{{ $settings->exam_weight }}%</span></p>
                </div>
            </div>
        </div>

        <!-- 5. Promotion Settings Card -->
        <div class="card hover:shadow-lg transition-all duration-200">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white">
                        <i class="fas fa-arrow-up text-xl"></i>
                    </div>
                    <a href="{{ route('settings.school.edit-promotion') }}" class="btn btn-sm btn-outline">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Promotion Settings</h3>
                <p class="text-sm text-gray-600 mb-4">Student promotion rules</p>
                
                @php
                    $promoSettings = $settings->promotion_settings ?? [];
                @endphp
                <div class="space-y-2 text-sm">
                    <p><span class="font-semibold text-gray-700">Pass Mark:</span> <span class="text-gray-900">{{ $promoSettings['pass_mark'] ?? 50 }}%</span></p>
                    <p><span class="font-semibold text-gray-700">Auto Promotion:</span> 
                        @if($promoSettings['auto_promotion'] ?? false)
                            <span class="badge badge-success">Enabled</span>
                        @else
                            <span class="badge badge-secondary">Disabled</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- 6. Results Management Card
        <div class="card hover:shadow-lg transition-all duration-200">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-teal-500 to-teal-600 flex items-center justify-center text-white">
                        <i class="fas fa-file-alt text-xl"></i>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('results.index') }}" class="btn btn-sm btn-outline" title="Results Dashboard">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Results Management</h3>
                <p class="text-sm text-gray-600 mb-4">Enter and manage student results by session and term</p>

                <div class="space-y-2 text-sm">
                    <p><span class="font-semibold text-gray-700">Entry:</span> <span class="text-gray-900">Session & term based</span></p>
                    <p><span class="font-semibold text-gray-700">Grading:</span> <span class="text-gray-900">Central grade scale</span></p>
                    <p><span class="font-semibold text-gray-700">Reporting:</span> <span class="text-gray-900">Session and term report cards</span></p>
                </div>
            </div>
        </div> --}}

        <!-- 6. Financial Settings Card -->
        <div class="card hover:shadow-lg transition-all duration-200">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white">
                        <i class="fas fa-money-bill-wave text-xl"></i>
                    </div>
                    <a href="{{ route('settings.school.edit-financial') }}" class="btn btn-sm btn-outline">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Financial Settings</h3>
                <p class="text-sm text-gray-600 mb-4">Payment methods and bank details</p>
                
                <div class="space-y-2 text-sm">
                    <p>
                        <i class="fas fa-{{ $settings->bank_name ? 'check text-success-600' : 'times text-gray-400' }} mr-2"></i>
                        <span>{{ $settings->bank_name ? $settings->bank_name : 'Bank details not set' }}</span>
                    </p>
                    <p>
                        <i class="fas fa-{{ $settings->mobile_money_provider ? 'check text-success-600' : 'times text-gray-400' }} mr-2"></i>
                        <span>{{ $settings->mobile_money_provider ? $settings->mobile_money_provider . ' Mobile Money' : 'Mobile Money not set' }}</span>
                    </p>
                    <p>
                        <i class="fas fa-{{ $settings->finance_contact_email ? 'check text-success-600' : 'times text-gray-400' }} mr-2"></i>
                        <span>{{ $settings->finance_contact_email ? 'Finance contact set' : 'Finance contact not set' }}</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- 7. Paystack Settings Card -->
        <div class="card hover:shadow-lg transition-all duration-200">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white">
                        <i class="fas fa-credit-card text-xl"></i>
                    </div>
                    <a href="{{ route('settings.school.edit-paystack') }}" class="btn btn-sm btn-outline">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Paystack Integration</h3>
                <p class="text-sm text-gray-600 mb-4">Online payment gateway configuration</p>
                
                <div class="space-y-2 text-sm">
                    <p>
                        @if($settings->enable_online_payment)
                            <i class="fas fa-check-circle text-success-600 mr-2"></i>
                            <span class="text-success-600 font-semibold">Online Payments Enabled</span>
                        @else
                            <i class="fas fa-times-circle text-gray-400 mr-2"></i>
                            <span class="text-gray-600">Online Payments Disabled</span>
                        @endif
                    </p>
                    <p>
                        <i class="fas fa-{{ $settings->paystack_public_key ? 'check text-success-600' : 'times text-gray-400' }} mr-2"></i>
                        <span>{{ $settings->paystack_public_key ? 'Public key configured' : 'Public key not set' }}</span>
                    </p>
                    <p>
                        <i class="fas fa-{{ $settings->paystack_secret_key ? 'check text-success-600' : 'times text-gray-400' }} mr-2"></i>
                        <span>{{ $settings->paystack_secret_key ? 'Secret key configured' : 'Secret key not set' }}</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- 7. System Preferences Card -->
        <div class="card hover:shadow-lg transition-all duration-200">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center text-white">
                        <i class="fas fa-sliders-h text-xl"></i>
                    </div>
                    <a href="{{ route('settings.school.edit-preferences') }}" class="btn btn-sm btn-outline">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">System Preferences</h3>
                <p class="text-sm text-gray-600 mb-4">System-wide feature toggles</p>
                
                <div class="space-y-1 text-sm">
                    <p>
                        <i class="fas fa-{{ $settings->teachers_can_enter_scores ? 'check text-success-600' : 'times text-gray-400' }} mr-2"></i>
                        <span>Teachers can enter scores</span>
                    </p>
                    <p>
                        <i class="fas fa-{{ $settings->parents_can_view_results ? 'check text-success-600' : 'times text-gray-400' }} mr-2"></i>
                        <span>Parents can view results</span>
                    </p>
                    <p>
                        <i class="fas fa-{{ $settings->require_daily_attendance ? 'check text-success-600' : 'times text-gray-400' }} mr-2"></i>
                        <span>Require daily attendance</span>
                    </p>
                </div>
            </div>
        </div>

    </div>

    <!-- Additional Info Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Curriculum Overview -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-info-circle mr-2 text-primary-600"></i>Current Configuration
                </h3>
            </div>
            <div class="card-body space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-600 font-semibold mb-1">ACADEMIC SESSION</p>
                        <p class="text-lg font-bold text-gray-900">{{ $settings->current_session }}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-600 font-semibold mb-1">CURRENT TERM</p>
                        <p class="text-lg font-bold text-gray-900">{{ $settings->current_term }}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-600 font-semibold mb-1">PASSING SCORE</p>
                        <p class="text-lg font-bold text-gray-900">{{ $settings->passing_score }}%</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-600 font-semibold mb-1">CURRENCY</p>
                        <p class="text-lg font-bold text-gray-900">{{ $settings->currency }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grade Boundaries Quick View -->
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-star mr-2 text-warning-600"></i>Grade Boundaries
                </h3>
                <a href="{{ route('settings.school.edit-grading') }}" class="btn btn-sm btn-outline">Edit</a>
            </div>
            <div class="card-body">
                <div class="space-y-2">
                    @php
                        $boundaries = $settings->grade_boundaries ?? ['A' => 80, 'B' => 70, 'C' => 60, 'D' => 50, 'E' => 40, 'F' => 0];
                        $grades = ['A', 'B', 'C', 'D', 'E', 'F'];
                    @endphp
                    @foreach($grades as $index => $grade)
                        @php
                            $minScore = $boundaries[$grade] ?? 0;
                            $nextIndex = $index + 1;
                            $maxScore = $nextIndex < count($grades) ? $boundaries[$grades[$nextIndex]] - 1 : 100;
                        @endphp
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                            <div>
                                <span class="font-bold text-lg">{{ $grade }}</span>
                                <span class="text-sm text-gray-600 ml-2">({{ $minScore }}-{{ $maxScore }}%)</span>
                            </div>
                            <span class="badge badge-primary">{{ $minScore }}% min</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
