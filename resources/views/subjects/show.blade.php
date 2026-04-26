@extends('layouts.spa')

@section('title', $subject->name)

@section('breadcrumb')
    <span class="text-gray-400">Academics</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('subjects.index') }}" class="text-primary-600 hover:text-primary-700">Subjects</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">{{ $subject->name }}</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="card">
        <div class="card-body">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">
                <!-- Subject Info -->
                <div class="flex-1">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-16 h-16 rounded-lg bg-{{ $subject->color }}-100 flex items-center justify-center">
                            <i class="fas fa-book text-{{ $subject->color }}-600 text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ $subject->name }}</h1>
                            <p class="text-gray-600 mt-1">Code: <span class="font-mono font-semibold">{{ $subject->code }}</span></p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        @if($subject->category)
                            <span class="badge badge-secondary">{{ $subject->category }}</span>
                        @endif
                        @if($subject->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('subjects.edit', $subject) }}" class="btn btn-primary">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                    <a href="{{ route('subjects.index') }}" class="btn btn-outline">
                        <i class="fas fa-arrow-left mr-2"></i>Back to List
                    </a>
                    <form action="{{ route('subjects.destroy', $subject) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this subject? This will remove it from all classes.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash mr-2"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Description -->
            @if($subject->description)
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-file-alt mr-2 text-primary-600"></i>Description
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-gray-700">{{ $subject->description }}</p>
                </div>
            </div>
            @endif

            <!-- Classes Teaching This Subject -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-school mr-2 text-primary-600"></i>Assign to Classes
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-sm text-gray-600 mb-4">Select classes to add this subject. You can modify teacher assignments and periods per week on the class subject management page.</p>
                    
                    @php
                        $classIds = $subject->classes->pluck('id')->toArray();
                    @endphp

                    <form id="assignClassesForm" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @forelse(\App\Models\SchoolClass::active()->get() as $class)
                                <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                    <input type="checkbox" name="class_ids[]" value="{{ $class->id }}" 
                                           {{ in_array($class->id, $classIds) ? 'checked' : '' }}
                                           class="w-4 h-4 text-primary-600 rounded">
                                    <span class="flex-1">
                                        <span class="block font-medium text-gray-900">{{ $class->full_name }}</span>
                                        <span class="block text-xs text-gray-600">{{ $class->current_enrollment ?? 0 }} students</span>
                                    </span>
                                </label>
                            @empty
                                <p class="text-gray-500 col-span-2">No classes available</p>
                            @endforelse
                        </div>

                        @if(\App\Models\SchoolClass::active()->count() > 0)
                        <div class="flex items-center gap-2 pt-4">
                            <button type="button" onclick="submitClassAssignment()" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>Save Assignments
                            </button>
                            <button type="reset" class="btn btn-outline">
                                Reset
                            </button>
                        </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Classes Teaching This Subject -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-list mr-2 text-primary-600"></i>Classes ({{ $subject->classes->count() }})
                    </h3>
                </div>
                <div class="card-body">
                    @if($subject->classes->count() > 0)
                    <div class="space-y-3">
                        @foreach($subject->classes as $class)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $class->full_name }}</h4>
                                            <div class="flex items-center gap-4 mt-1 text-sm text-gray-600">
                                                @if($class->teacher?->profile)
                                                    <span>
                                                        <i class="fas fa-user mr-1"></i>{{ $class->teacher->profile->first_name }} {{ $class->teacher->profile->last_name }}
                                                    </span>
                                                @endif
                                                @php
                                                    $teacher = $class->pivot->teacher_id ? \App\Models\UserProfile::find($class->pivot->teacher_id) : null;
                                                @endphp
                                                @if($teacher)
                                                    <span class="text-{{ $subject->color }}-600">
                                                        <i class="fas fa-chalkboard-teacher mr-1"></i>{{ $teacher->first_name }} {{ $teacher->last_name }}
                                                    </span>
                                                @endif
                                                <span class="badge badge-{{ $subject->color }}">
                                                    {{ $class->pivot->periods_per_week }} periods/week
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('classes.subjects.index', $class) }}" class="btn btn-xs btn-outline">
                                    <i class="fas fa-external-link-alt mr-1"></i>Manage
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-school text-4xl text-gray-300 mb-3"></i>
                        <p>This subject is not assigned to any classes yet</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-chart-bar mr-2 text-primary-600"></i>Statistics
                    </h3>
                </div>
                <div class="card-body space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Classes</span>
                        <span class="text-2xl font-bold text-primary-600">{{ $subject->classes->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Total Students</span>
                        <span class="text-2xl font-bold text-primary-600">
                            {{ $subject->classes->sum(function($class) { return $class->current_enrollment; }) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Status</span>
                        @if($subject->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Subject Details -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-info-circle mr-2 text-primary-600"></i>Details
                    </h3>
                </div>
                <div class="card-body space-y-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">Subject Code</p>
                        <p class="text-gray-900 font-mono font-semibold">{{ $subject->code }}</p>
                    </div>

                    @if($subject->category)
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">Category</p>
                        <p class="text-gray-900">{{ $subject->category }}</p>
                    </div>
                    @endif

                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">Display Color</p>
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded bg-{{ $subject->color }}-500"></div>
                            <span class="text-gray-900 capitalize">{{ $subject->color }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Information -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-database mr-2 text-primary-600"></i>System Info
                    </h3>
                </div>
                <div class="card-body space-y-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">Created</p>
                        <p class="text-gray-900 text-sm">{{ $subject->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">Last Updated</p>
                        <p class="text-gray-900 text-sm">{{ $subject->updated_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function submitClassAssignment() {
    const form = document.getElementById('assignClassesForm');
    const classIds = Array.from(form.querySelectorAll('input[name="class_ids[]"]:checked')).map(el => el.value);
    
    fetch('{{ route("subjects.assign-classes", $subject) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ class_ids: classIds })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Classes assigned successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to assign classes'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while assigning classes');
    });
}
</script>
@endsection
