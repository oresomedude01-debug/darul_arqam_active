@extends('layouts.spa')

@section('title', 'Add Event')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
            <a href="{{ route('calendar.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium mb-6 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to Calendar
            </a>
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Create New Event</h1>
                <p class="text-gray-600">Add an important event to the school calendar</p>
            </div>
        </div>

        <!-- Main Form Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <form method="POST" action="{{ route('events.store') }}" class="p-8">
                @csrf

                <!-- Section 1: Basic Information -->
                <div class="mb-8">
                    <div class="flex items-center mb-6">
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold text-sm">1</div>
                        <h2 class="text-xl font-bold text-gray-900 ml-3">Basic Information</h2>
                    </div>
                    
                    <div class="space-y-6">
                        <!-- Event Title -->
                        <div>
                            <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                                Event Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   placeholder="e.g., Mid-term Exams, Annual Sports Day"
                                   required>
                            @error('title')
                                <div class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg flex items-start gap-2">
                                    <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
                                    <p class="text-red-700 text-sm">{{ $message }}</p>
                                </div>
                            @enderror
                        </div>

                        <!-- Event Type -->
                        <div>
                            <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">
                                Event Type <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach(['holiday' => '🏖️ Holiday', 'exam' => '📝 Exam', 'break' => '⏸️ Break', 'meeting' => '👥 Meeting', 'celebration' => '🎉 Celebration', 'other' => '📌 Other'] as $value => $label)
                                <label class="relative">
                                    <input type="radio" name="type" value="{{ $value }}" 
                                           {{ old('type') === $value ? 'checked' : '' }}
                                           class="sr-only peer" required>
                                    <div class="p-3 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50 transition hover:border-gray-300">
                                        <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @error('type')
                                <div class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg flex items-start gap-2">
                                    <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
                                    <p class="text-red-700 text-sm">{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-8">

                <!-- Section 2: Date & Time -->
                <div class="mb-8">
                    <div class="flex items-center mb-6">
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold text-sm">2</div>
                        <h2 class="text-xl font-bold text-gray-900 ml-3">Date & Time</h2>
                    </div>

                    <div class="space-y-6">
                        <!-- Date Range -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Start Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                       required>
                                @error('start_date')
                                    <div class="mt-2 p-2 bg-red-50 border border-red-200 rounded text-red-700 text-sm">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                    End Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                       required>
                                @error('end_date')
                                    <div class="mt-2 p-2 bg-red-50 border border-red-200 rounded text-red-700 text-sm">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Time Range -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start gap-3 mb-4">
                                <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                                <p class="text-sm text-blue-800">Leave time fields empty for all-day events</p>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="start_time" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Start Time
                                    </label>
                                    <input type="time" id="start_time" name="start_time" value="{{ old('start_time') }}" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                    @error('start_time')
                                        <div class="mt-2 text-red-600 text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label for="end_time" class="block text-sm font-semibold text-gray-700 mb-2">
                                        End Time
                                    </label>
                                    <input type="time" id="end_time" name="end_time" value="{{ old('end_time') }}" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                    @error('end_time')
                                        <div class="mt-2 text-red-600 text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-8">

                <!-- Section 3: Details & Appearance -->
                <div class="mb-8">
                    <div class="flex items-center mb-6">
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold text-sm">3</div>
                        <h2 class="text-xl font-bold text-gray-900 ml-3">Details & Appearance</h2>
                    </div>

                    <div class="space-y-6">
                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                Description <span class="text-gray-500 text-xs font-normal">(Optional)</span>
                            </label>
                            <textarea id="description" name="description" rows="4" 
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                      placeholder="Add any additional details, requirements, or notes about this event...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="mt-2 text-red-600 text-sm">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Color Picker -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="color" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Event Color
                                </label>
                                <div class="flex items-center gap-3">
                                    <input type="color" id="color" name="color" value="{{ old('color', '#3b82f6') }}" 
                                           class="h-14 w-20 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-gray-400 transition">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Hex Value:</p>
                                        <input type="text" value="{{ old('color', '#3b82f6') }}" 
                                               class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 font-mono w-28" readonly>
                                    </div>
                                </div>
                                @error('color')
                                    <div class="mt-2 text-red-600 text-sm">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Quick Color Presets -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Quick Colors</label>
                                <div class="flex gap-2">
                                    @foreach(['#f59e0b' => 'Holiday', '#8b5cf6' => 'Exam', '#ef4444' => 'Break', '#3b82f6' => 'Meeting', '#06b6d4' => 'Celebration'] as $color => $name)
                                    <button type="button" class="w-8 h-8 rounded-lg border-2 border-transparent hover:border-gray-300 transition" 
                                            style="background-color: {{ $color }}" 
                                            onclick="document.getElementById('color').value='{{ $color }}'; document.querySelector('input[readonly]').value='{{ $color }}'; this.parentElement.querySelector('button').focus();"
                                            title="{{ $name }}"></button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-8">

                <!-- Section 4: Organization -->
                <div class="mb-8">
                    <div class="flex items-center mb-6">
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold text-sm">4</div>
                        <h2 class="text-xl font-bold text-gray-900 ml-3">Organization</h2>
                    </div>

                    <div class="space-y-6">
                        <!-- Academic Term -->
                        <div>
                            <label for="academic_term_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                Academic Term <span class="text-gray-500 text-xs font-normal">(Optional)</span>
                            </label>
                            <select id="academic_term_id" name="academic_term_id" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                <option value="">-- Select a term --</option>
                                @foreach($academicTerms as $term)
                                    <option value="{{ $term->id }}" {{ old('academic_term_id') === (string)$term->id ? 'selected' : '' }}>
                                        {{ $term->term_name }} ({{ $term->academic_year }})
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_term_id')
                                <div class="mt-2 text-red-600 text-sm">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Affected Classes -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Affected Classes <span class="text-gray-500 text-xs font-normal">(Optional - leave empty for all)</span>
                            </label>
                            <div class="border-2 border-gray-300 rounded-lg p-4 max-h-48 overflow-y-auto bg-gray-50">
                                @if($classes->count() > 0)
                                    <div class="space-y-3">
                                        @foreach($classes as $class)
                                            <label class="flex items-center gap-3 cursor-pointer hover:bg-white p-2 rounded transition">
                                                <input type="checkbox" name="affected_classes[]" value="{{ $class->id }}" 
                                                       {{ in_array($class->id, old('affected_classes', [])) ? 'checked' : '' }}
                                                       class="w-4 h-4 text-blue-600 rounded border-gray-300">
                                                <div class="flex-1">
                                                    <span class="text-sm font-medium text-gray-900">{{ $class->class_name }}</span>
                                                    <span class="text-xs text-gray-500 ml-2">({{ $class->section }})</span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500 text-center py-4">No classes available</p>
                                @endif
                            </div>
                            @error('affected_classes')
                                <div class="mt-2 text-red-600 text-sm">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between gap-4 pt-8 border-t-2 border-gray-200">
                    <a href="{{ route('calendar.index') }}" class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 shadow-lg transition transform hover:scale-105">
                        <i class="fas fa-check mr-2"></i>Create Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Update hex value display when color picker changes
    document.getElementById('color').addEventListener('change', function() {
        document.querySelector('input[readonly]').value = this.value;
    });

    // Auto-fill end date if not set
    document.getElementById('start_date').addEventListener('change', function() {
        if (!document.getElementById('end_date').value) {
            document.getElementById('end_date').value = this.value;
        }
    });
</script>
@endsection
