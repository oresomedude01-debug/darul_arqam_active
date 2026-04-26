@extends('layouts.spa')

@section('title', 'Edit School Operating Days')

@section('breadcrumb')
    <span class="text-gray-400">Settings</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('settings.school.index') }}" class="text-primary-600 hover:text-primary-700">School Settings</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">School Operating Days</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">School Operating Days</h1>
            <p class="text-sm text-gray-600 mt-1">Select which days your school operates</p>
        </div>
        <a href="{{ route('settings.school.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left mr-2"></i>Back to Settings
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('settings.school.update-school-days') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Operating Days -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-calendar-days mr-2 text-primary-600"></i>Select Operating Days
                </h3>
            </div>
            <div class="card-body">
                <p class="text-sm text-gray-600 mb-6">Check the days your school operates. This affects timetables, attendance, and other scheduling features.</p>

                @php
                    $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    $operatingDays = $settings->getOperatingDays() ?? [];
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($daysOfWeek as $day)
                        <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-primary-50 cursor-pointer transition-colors">
                            <input type="checkbox" name="school_days[]" value="{{ $day }}" 
                                @checked(in_array($day, $operatingDays))
                                class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <span class="ml-3 font-medium text-gray-900">{{ $day }}</span>
                            
                            @if(in_array($day, ['Saturday', 'Sunday']))
                                <span class="ml-auto text-xs bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full">Weekend</span>
                            @else
                                <span class="ml-auto text-xs bg-green-100 text-green-800 px-3 py-1 rounded-full">Weekday</span>
                            @endif
                        </label>
                    @endforeach
                </div>

                @error('school_days')
                    <p class="text-red-500 text-sm mt-4">{{ $message }}</p>
                @enderror

                <!-- Summary -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h4 class="font-semibold text-gray-900 mb-3">Summary</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-primary-600">
                                <span id="total-days">0</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">Total Operating Days</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-green-600">
                                <span id="weekdays">0</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">Weekdays</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-yellow-600">
                                <span id="weekends">0</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">Weekend Days</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Information Alert -->
        <div class="alert alert-info">
            <i class="fas fa-lightbulb mr-2"></i>
            <strong>Tip:</strong> Most schools operate Monday through Friday. If your school operates on weekends, select the appropriate days. These settings affect timetable generation, attendance tracking, and holiday schedules.
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
    function updateSummary() {
        const checkboxes = document.querySelectorAll('input[name="school_days[]"]');
        const checked = Array.from(checkboxes).filter(cb => cb.checked);
        const weekdays = checked.filter(cb => !['Saturday', 'Sunday'].includes(cb.value)).length;
        const weekends = checked.filter(cb => ['Saturday', 'Sunday'].includes(cb.value)).length;
        
        document.getElementById('total-days').textContent = checked.length;
        document.getElementById('weekdays').textContent = weekdays;
        document.getElementById('weekends').textContent = weekends;
    }

    // Update summary on page load and when checkboxes change
    document.addEventListener('DOMContentLoaded', updateSummary);
    document.querySelectorAll('input[name="school_days[]"]').forEach(cb => {
        cb.addEventListener('change', updateSummary);
    });
</script>
@endsection
