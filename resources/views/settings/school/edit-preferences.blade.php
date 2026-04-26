@extends('layouts.spa')

@section('title', 'Edit System Preferences')

@section('breadcrumb')
    <span class="text-gray-400">Settings</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('settings.school.index') }}" class="text-primary-600 hover:text-primary-700">School Settings</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">System Preferences</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">System Preferences</h1>
            <p class="text-sm text-gray-600 mt-1">Configure which features are enabled in the system</p>
        </div>
        <a href="{{ route('settings.school.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left mr-2"></i>Back to Settings
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('settings.school.update-preferences') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Teaching Features -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-chalkboard-user mr-2 text-primary-600"></i>Teaching Features
                </h3>
            </div>
            <div class="card-body space-y-4">
                <label class="flex items-start p-4 border border-gray-200 rounded-lg hover:bg-primary-50 cursor-pointer transition-colors">
                    <input type="hidden" name="teachers_can_enter_scores" value="0">
                    <input type="checkbox" name="teachers_can_enter_scores" value="1" @checked($settings->teachers_can_enter_scores) class="mt-1 w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <div class="ml-3 flex-1">
                        <span class="font-medium text-gray-900 block">Teachers Can Enter Scores</span>
                        <p class="text-sm text-gray-600 mt-1">Allow teachers to enter continuous assessment (CA) scores and exam scores for their classes.</p>
                    </div>
                </label>

                <label class="flex items-start p-4 border border-gray-200 rounded-lg hover:bg-primary-50 cursor-pointer transition-colors">
                    <input type="hidden" name="require_daily_attendance" value="0">
                    <input type="checkbox" name="require_daily_attendance" value="1" @checked($settings->require_daily_attendance) class="mt-1 w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <div class="ml-3 flex-1">
                        <span class="font-medium text-gray-900 block">Require Daily Attendance</span>
                        <p class="text-sm text-gray-600 mt-1">Teachers must mark attendance for every school day. Unchecked means attendance is optional.</p>
                    </div>
                </label>
            </div>
        </div>

        <!-- Access Features -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-eye mr-2 text-primary-600"></i>Access Features
                </h3>
            </div>
            <div class="card-body space-y-4">
                <label class="flex items-start p-4 border border-gray-200 rounded-lg hover:bg-primary-50 cursor-pointer transition-colors">
                    <input type="hidden" name="parents_can_view_results" value="0">
                    <input type="checkbox" name="parents_can_view_results" value="1" @checked($settings->parents_can_view_results) class="mt-1 w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <div class="ml-3 flex-1">
                        <span class="font-medium text-gray-900 block">Parents Can View Results</span>
                        <p class="text-sm text-gray-600 mt-1">Allow parents to view their children's exam results, grades, and performance analytics through their portal.</p>
                    </div>
                </label>

                <label class="flex items-start p-4 border border-gray-200 rounded-lg hover:bg-primary-50 cursor-pointer transition-colors">
                    <input type="hidden" name="parents_can_view_attendance" value="0">
                    <input type="checkbox" name="parents_can_view_attendance" value="1" @checked($settings->parents_can_view_attendance ?? false) class="mt-1 w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <div class="ml-3 flex-1">
                        <span class="font-medium text-gray-900 block">Parents Can View Attendance</span>
                        <p class="text-sm text-gray-600 mt-1">Allow parents to view their children's attendance records and absence notifications.</p>
                    </div>
                </label>
            </div>
        </div>

        <!-- Communication Features -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-bell mr-2 text-primary-600"></i>Communication Features
                </h3>
            </div>
            <div class="card-body space-y-4">
                <label class="flex items-start p-4 border border-gray-200 rounded-lg hover:bg-primary-50 cursor-pointer transition-colors">
                    <input type="hidden" name="enable_notifications" value="0">
                    <input type="checkbox" name="enable_notifications" value="1" @checked($settings->enable_notifications) class="mt-1 w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <div class="ml-3 flex-1">
                        <span class="font-medium text-gray-900 block">Enable System Notifications</span>
                        <p class="text-sm text-gray-600 mt-1">Send automated notifications to parents, students, and teachers about important updates (results, attendance, events).</p>
                    </div>
                </label>
            </div>
        </div>

        <!-- Module Features -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-layer-group mr-2 text-primary-600"></i>Module Features
                </h3>
            </div>
            <div class="card-body space-y-4">
                <label class="flex items-start p-4 border border-gray-200 rounded-lg hover:bg-primary-50 cursor-pointer transition-colors">
                    <input type="hidden" name="enable_fees_module" value="0">
                    <input type="checkbox" name="enable_fees_module" value="1" @checked($settings->enable_fees_module) class="mt-1 w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <div class="ml-3 flex-1">
                        <span class="font-medium text-gray-900 block">Enable Fees Management Module</span>
                        <p class="text-sm text-gray-600 mt-1">Enable the school fees module for managing student fees, payments, and receipts.</p>
                    </div>
                </label>

                <label class="flex items-start p-4 border border-gray-200 rounded-lg hover:bg-primary-50 cursor-pointer transition-colors">
                    <input type="hidden" name="enable_library_module" value="0">
                    <input type="checkbox" name="enable_library_module" value="1" @checked($settings->enable_library_module ?? false) class="mt-1 w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <div class="ml-3 flex-1">
                        <span class="font-medium text-gray-900 block">Enable Library Management Module</span>
                        <p class="text-sm text-gray-600 mt-1">Enable the library module for managing books, borrowing, and returns.</p>
                    </div>
                </label>
            </div>
        </div>

        <!-- Status Summary -->
        <div class="card bg-gradient-to-br from-gray-50 to-gray-100">
            <div class="card-header bg-transparent border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-list-check mr-2 text-primary-600"></i>Current Feature Status
                </h3>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-center justify-between p-3 bg-white rounded border border-gray-200">
                        <span class="text-gray-700 font-medium">Teaching Features</span>
                        <span class="text-lg">@if($settings->teachers_can_enter_scores) <i class="fas fa-check-circle text-green-600"></i> @else <i class="fas fa-times-circle text-gray-400"></i> @endif</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-white rounded border border-gray-200">
                        <span class="text-gray-700 font-medium">Parent Portal Access</span>
                        <span class="text-lg">@if($settings->parents_can_view_results) <i class="fas fa-check-circle text-green-600"></i> @else <i class="fas fa-times-circle text-gray-400"></i> @endif</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-white rounded border border-gray-200">
                        <span class="text-gray-700 font-medium">Attendance Tracking</span>
                        <span class="text-lg">@if($settings->require_daily_attendance) <i class="fas fa-check-circle text-green-600"></i> @else <i class="fas fa-times-circle text-gray-400"></i> @endif</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-white rounded border border-gray-200">
                        <span class="text-gray-700 font-medium">Notifications</span>
                        <span class="text-lg">@if($settings->enable_notifications) <i class="fas fa-check-circle text-green-600"></i> @else <i class="fas fa-times-circle text-gray-400"></i> @endif</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-white rounded border border-gray-200">
                        <span class="text-gray-700 font-medium">Fees Module</span>
                        <span class="text-lg">@if($settings->enable_fees_module) <i class="fas fa-check-circle text-green-600"></i> @else <i class="fas fa-times-circle text-gray-400"></i> @endif</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-white rounded border border-gray-200">
                        <span class="text-gray-700 font-medium">Library Module</span>
                        <span class="text-lg">@if($settings->enable_library_module ?? false) <i class="fas fa-check-circle text-green-600"></i> @else <i class="fas fa-times-circle text-gray-400"></i> @endif</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between">
            <a href="{{ route('settings.school.index') }}" class="btn btn-outline">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-2"></i>Save Changes
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateStatus);
        });

        function updateStatus() {
            // Find and update the status summary
            location.reload(); // Simple approach - reload page to update status summary
        }
    });
</script>
@endsection
