@extends('layouts.spa')

@section('title', 'Manage Timetable - ' . $class->full_name)

@section('breadcrumb')
    <span class="text-gray-400">Classes</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('classes.index') }}" class="text-primary-600 hover:text-primary-700">All Classes</a>
    <span class="text-gray-400">/</span>
    <a href="{{ route('classes.show', $class) }}" class="text-primary-600 hover:text-primary-700">{{ $class->full_name }}</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Timetable</span>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{
    showAddModal: false,
    editingEntry: null,
    addForm: {
        type: 'class',
        subject_id: '',
        teacher_id: '',
        day_of_week: 'monday',
        start_time: '08:00',
        end_time: '09:00',
        period_number: 1,
        room_number: '',
        notes: ''
    },
    resetAddForm() {
        this.addForm = {
            type: 'class',
            subject_id: '',
            teacher_id: '',
            day_of_week: 'monday',
            start_time: '08:00',
            end_time: '09:00',
            period_number: 1,
            room_number: '',
            notes: ''
        };
    }
}">
    <!-- Flash Messages Section -->
    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-6 shadow-md animate-pulse">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-500 text-2xl mt-1"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-red-900 mb-3">Validation Errors</h3>
                    <ul class="space-y-2">
                        @foreach ($errors->all() as $error)
                            <li class="flex items-start gap-2">
                                <span class="text-red-600 font-bold mt-0.5">•</span>
                                <span class="text-red-800">{{ $error }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    @if (session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-6 shadow-md animate-pulse" x-data="{ show: true }" x-show="show" @click.away="show = false">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-500 text-2xl mt-1"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-green-900 mb-1">Success!</h3>
                    <p class="text-green-800 leading-relaxed">{{ session('success') }}</p>
                </div>
                <button type="button" @click="show = false" class="flex-shrink-0 text-green-400 hover:text-green-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-6 shadow-md animate-pulse" x-data="{ show: true }" x-show="show" @click.away="show = false">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <i class="fas fa-times-circle text-red-500 text-2xl mt-1"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-red-900 mb-2">Error Occurred</h3>
                    <div class="text-red-800 leading-relaxed">
                        <p class="mb-3">{{ session('error') }}</p>
                        <div class="bg-red-100 border border-red-300 rounded p-3 mt-3">
                            <p class="text-sm text-red-700 font-semibold mb-2"><i class="fas fa-lightbulb mr-2"></i>How to Fix:</p>
                            <ul class="text-sm text-red-700 space-y-1 ml-4">
                                @if (str_contains(session('error'), 'Teacher Conflict'))
                                    <li>• Choose a different teacher for this period</li>
                                    <li>• Or select a different time slot</li>
                                    <li>• View the teacher's full schedule before assigning</li>
                                @elseif (str_contains(session('error'), 'Class Conflict'))
                                    <li>• Select a different time slot for this class</li>
                                    <li>• Check the current timetable for existing periods</li>
                                @elseif (str_contains(session('error'), 'Invalid Day'))
                                    <li>• Only create periods on designated school operating days</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                <button type="button" @click="show = false" class="flex-shrink-0 text-red-400 hover:text-red-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
    @endif

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Manage Timetable</h1>
            <p class="text-sm text-gray-600 mt-1">{{ $class->full_name }}</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="showAddModal = true; resetAddForm();" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>Add Period
            </button>
            <a href="{{ route('classes.show', $class) }}" class="btn btn-outline">
                <i class="fas fa-arrow-left mr-2"></i>Back to Class
            </a>
        </div>
    </div>

    <!-- Operating Days Info Alert -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start gap-3">
            <i class="fas fa-info-circle text-blue-600 text-lg mt-0.5"></i>
            <div>
                <p class="font-semibold text-blue-900">School Operating Days</p>
                <p class="text-sm text-blue-700 mt-1">
                    Timetable entries can only be created for school operating days: 
                    <span class="font-semibold">{{ implode(', ', $operatingDays) }}</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Periods</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $class->timetables->count() }}</p>
                    </div>
                    <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar text-primary-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Class Periods</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">{{ $class->timetables->where('type', 'class')->count() }}</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book text-blue-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Breaks</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ $class->timetables->whereIn('type', ['break', 'lunch'])->count() }}</p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-coffee text-green-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Subjects</p>
                        <p class="text-2xl font-bold text-purple-600 mt-1">{{ $subjects->count() }}</p>
                    </div>
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book-open text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Timetable Grid -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-calendar-week mr-2 text-primary-600"></i>Weekly Timetable
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase border-r border-gray-200" style="width: 120px;">Time / Period</th>
                            @foreach($days as $day)
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase border-r border-gray-200 last:border-r-0">
                                    {{ ucfirst($day) }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            // Group timetable entries by period number and time
                            $timetableGrid = [];
                            foreach($class->timetables as $entry) {
                                $timeSlot = $entry->start_time->format('H:i') . ' - ' . $entry->end_time->format('H:i');
                                $key = $entry->period_number . '_' . $timeSlot;

                                if (!isset($timetableGrid[$key])) {
                                    $timetableGrid[$key] = [
                                        'period_number' => $entry->period_number,
                                        'time_slot' => $timeSlot,
                                        'entries' => []
                                    ];
                                }

                                $timetableGrid[$key]['entries'][$entry->day_of_week] = $entry;
                            }

                            // Sort by period number
                            uasort($timetableGrid, function($a, $b) {
                                return $a['period_number'] <=> $b['period_number'];
                            });
                        @endphp

                        @forelse($timetableGrid as $key => $periodData)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 border-r border-gray-200 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">Period {{ $periodData['period_number'] }}</div>
                                <div class="text-xs text-gray-500">{{ $periodData['time_slot'] }}</div>
                            </td>
                            @foreach($days as $day)
                                @php
                                    $entry = $periodData['entries'][$day] ?? null;
                                @endphp
                                <td class="px-2 py-2 border-r border-gray-200 last:border-r-0 align-top">
                                    @if($entry)
                                        @if($entry->type === 'break')
                                            <div class="bg-green-50 border border-green-200 rounded p-2 text-center cursor-pointer hover:bg-green-100 transition-colors group relative">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-xs font-semibold text-green-700 uppercase flex-1">Break</p>
                                                    <form action="{{ route('classes.timetable.destroy', [$class, $entry]) }}" method="POST" class="inline" onsubmit="return confirm('Delete this period?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-800 hover:bg-red-100 rounded p-1 transition-colors" title="Delete">
                                                            <i class="fas fa-trash text-sm"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @elseif($entry->type === 'lunch')
                                            <div class="bg-orange-50 border border-orange-200 rounded p-2 text-center cursor-pointer hover:bg-orange-100 transition-colors group relative">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-xs font-semibold text-orange-700 uppercase flex-1">Lunch</p>
                                                    <form action="{{ route('classes.timetable.destroy', [$class, $entry]) }}" method="POST" class="inline" onsubmit="return confirm('Delete this period?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-800 hover:bg-red-100 rounded p-1 transition-colors" title="Delete">
                                                            <i class="fas fa-trash text-sm"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @elseif($entry->type === 'assembly')
                                            <div class="bg-purple-50 border border-purple-200 rounded p-2 text-center cursor-pointer hover:bg-purple-100 transition-colors group relative">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-xs font-semibold text-purple-700 uppercase flex-1">Assembly</p>
                                                    <form action="{{ route('classes.timetable.destroy', [$class, $entry]) }}" method="POST" class="inline" onsubmit="return confirm('Delete this period?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-800 hover:bg-red-100 rounded p-1 transition-colors" title="Delete">
                                                            <i class="fas fa-trash text-sm"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @else
                                            <div class="bg-blue-50 border border-blue-200 rounded p-2 hover:bg-blue-100 transition-colors group relative">
                                                <div class="flex items-start justify-between gap-2">
                                                    <div class="flex-1">
                                                        <p class="text-sm font-semibold text-blue-900 truncate">{{ $entry->subject ? $entry->subject->name : 'No Subject' }}</p>
                                                        <p class="text-xs text-blue-600 truncate mt-1">
                                                            <i class="fas fa-user text-xs mr-1"></i>{{ $entry->teacher ? $entry->teacher->full_name : 'No Teacher' }}
                                                        </p>
                                                        @if($entry->room_number)
                                                            <p class="text-xs text-blue-500 mt-1">
                                                                <i class="fas fa-door-open text-xs mr-1"></i>{{ $entry->room_number }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                    <form action="{{ route('classes.timetable.destroy', [$class, $entry]) }}" method="POST" class="inline" onsubmit="return confirm('Delete this period?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-800 hover:bg-red-100 rounded p-1 transition-colors" title="Delete">
                                                            <i class="fas fa-trash text-sm"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-center text-gray-300 py-2">
                                            <i class="fas fa-minus text-xs"></i>
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ count($days) + 1 }}" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-calendar-alt text-6xl text-gray-300 mb-4"></i>
                                <p class="text-lg font-medium mb-2">No timetable entries yet</p>
                                <p class="text-sm mb-4">Start by adding periods to create the weekly schedule</p>
                                <button @click="showAddModal = true; resetAddForm();" class="btn btn-primary">
                                    <i class="fas fa-plus mr-2"></i>Add First Period
                                </button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Period Modal -->
    <div x-show="showAddModal"
         x-cloak
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
         @click.self="showAddModal = false">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white"
             @click.stop>
            <form action="{{ route('classes.timetable.store', $class) }}" method="POST" class="space-y-4">
                @csrf

                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-plus-circle mr-2 text-primary-600"></i>Add Timetable Period
                    </h3>
                    <button type="button" @click="showAddModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Period Type <span class="text-red-500">*</span></label>
                        <select name="type" x-model="addForm.type" class="form-select" required>
                            <option value="class">Class</option>
                            <option value="break">Break</option>
                            <option value="lunch">Lunch</option>
                            <option value="assembly">Assembly</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Day <span class="text-red-500">*</span></label>
                        <select name="day_of_week" x-model="addForm.day_of_week" class="form-select" required>
                            @foreach($days as $day)
                                <option value="{{ $day }}">{{ ucfirst($day) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" x-show="addForm.type === 'class'">
                        <label class="form-label">Subject</label>
                        <select name="subject_id" x-model="addForm.subject_id" class="form-select">
                            <option value="">No Subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" x-show="addForm.type === 'class'">
                        <label class="form-label">Teacher</label>
                        <select name="teacher_id" x-model="addForm.teacher_id" class="form-select">
                            <option value="">No Teacher</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Start Time <span class="text-red-500">*</span></label>
                        <input type="time" name="start_time" x-model="addForm.start_time" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">End Time <span class="text-red-500">*</span></label>
                        <input type="time" name="end_time" x-model="addForm.end_time" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Period Number <span class="text-red-500">*</span></label>
                        <input type="number" name="period_number" x-model="addForm.period_number" min="1" class="form-input" required>
                    </div>

                    <div class="form-group" x-show="addForm.type === 'class'">
                        <label class="form-label">Room Number</label>
                        <input type="text" name="room_number" x-model="addForm.room_number" class="form-input" placeholder="e.g., Room 101">
                    </div>
                </div>

                <div class="form-group" x-show="addForm.type === 'class'">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" x-model="addForm.notes" rows="2" class="form-textarea" placeholder="Additional notes..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t">
                    <button type="button" @click="showAddModal = false" class="btn btn-outline">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i>Add Period
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
