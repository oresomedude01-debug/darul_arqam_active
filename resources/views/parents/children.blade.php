@extends('layouts.spa')

@section('content')
<div class="min-h-screen bg-gray-50 px-6 py-8">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-600 mb-6">
        <a href="{{ route('dashboard') }}" class="text-primary-600 hover:text-primary-700">Home</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="{{ route('admin.parents.index') }}" class="text-primary-600 hover:text-primary-700">Parents</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-900 font-medium">{{ $parent->first_name }}'s Children</span>
    </div>

    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $parent->first_name }}'s Children</h1>
            <p class="text-gray-600 mt-2">Manage student assignments for this parent</p>
        </div>
        <div class="flex gap-2">
            <button onclick="openAssignModal()" class="inline-flex items-center px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 gap-2">
                <i class="fas fa-plus"></i> Assign Student
            </button>
            <a href="{{ route('admin.parents.show', $parent->id) }}" class="inline-flex items-center px-6 py-3 bg-gray-300 text-gray-900 rounded-lg hover:bg-gray-400 gap-2">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Success Alert -->
    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Children Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Admission No</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Student Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Class</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($parent->children ?? [] as $child)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold">{{ $child->admission_number }}</td>
                        <td class="px-6 py-4">{{ $child->first_name }} {{ $child->last_name }}</td>
                        <td class="px-6 py-4">{{ $child->schoolClass->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                {{ $child->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($child->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('admin.parents.unassign-child', [$parent->id, $child->id]) }}"
                                  method="POST"
                                  onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-unlink"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            No children assigned yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ASSIGN STUDENT MODAL -->
<div id="assignModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow p-8 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold mb-6">Assign Student to Parent</h3>

        <form action="{{ route('admin.parents.assign-child', $parent->id) }}" method="POST">
            @csrf

            @php
                $allStudents = \App\Models\UserProfile::whereHas('user', function($q) {
                    $q->whereHas('roles', function($r) {
                        $r->where('slug', 'student');
                    });
                })->whereNull('parent_id')->get();
            @endphp

            <!-- SEARCHABLE SELECT -->
            <div x-data="studentSelect()" class="mb-6 relative">
                <label class="block text-sm font-medium mb-2">Select Student *</label>

                <input type="text"
                       x-model="search"
                       @focus="open = true"
                       placeholder="Type student name or admission number..."
                       class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                       required>

                <input type="hidden" name="student_id" :value="selectedId">

                <div x-show="open"
                     @click.outside="open = false"
                     class="absolute z-50 w-full mt-1 bg-white border rounded-lg shadow max-h-60 overflow-y-auto">

                    <template x-for="student in filteredStudents()" :key="student.id">
                        <div @click="selectStudent(student)"
                             class="px-4 py-2 hover:bg-primary-50 cursor-pointer">
                            <span x-text="student.name"></span>
                        </div>
                    </template>

                    <div x-show="filteredStudents().length === 0"
                         class="px-4 py-2 text-sm text-gray-500">
                        No student found
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" :disabled="!selectedId" :class="{'opacity-50 cursor-not-allowed': !selectedId}" class="flex-1 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 disabled:hover:bg-primary-600">
                    Assign
                </button>
                <button type="button"
                        onclick="closeAssignModal()"
                        class="flex-1 bg-gray-300 px-4 py-2 rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- SCRIPTS -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
function openAssignModal() {
    document.getElementById('assignModal').classList.remove('hidden');
}

function closeAssignModal() {
    document.getElementById('assignModal').classList.add('hidden');
}

function studentSelect() {
    return {
        open: false,
        search: '',
        selectedId: null,
        students: [
            @foreach($allStudents as $student)
            {
                id: {{ $student->user_id }},
                name: "{{ $student->first_name }} {{ $student->last_name }} ({{ $student->admission_number }})"
            },
            @endforeach
        ],
        filteredStudents() {
            return this.students.filter(s =>
                s.name.toLowerCase().includes(this.search.toLowerCase())
            );
        },
        selectStudent(student) {
            this.search = student.name;
            this.selectedId = student.id;
            console.log('Selected student ID:', this.selectedId);
            this.open = false;
        }
    }
}
</script>
@endsection
