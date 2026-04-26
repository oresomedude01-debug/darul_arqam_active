@extends('layouts.spa')

@section('title', 'Exam Types Setup')

@section('breadcrumb')
    <span class="text-gray-400">Grades</span>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Exam Types Setup</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success fade-in">
        <i class="fas fa-check-circle text-xl"></i>
        <p>{{ session('success') }}</p>
    </div>
    @endif

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Exam Types Setup</h1>
            <p class="text-gray-600 mt-1">Configure exam types and their weights</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('grades.scales') }}" class="btn btn-outline">
                <i class="fas fa-list mr-2"></i>
                Grade Scales
            </a>
            <button type="button" onclick="openAddModal()" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>
                Add Exam Type
            </button>
        </div>
    </div>

    <!-- Exam Types Table -->
    <div class="card">
        <div class="card-header">
            <h2 class="text-lg font-semibold text-gray-900">Exam Types</h2>
        </div>
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Weight (%)</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($examTypes as $examType)
                        <tr>
                            <td class="font-medium">{{ $examType->name }}</td>
                            <td>
                                <code class="bg-gray-100 px-2 py-1 rounded text-sm">{{ $examType->code }}</code>
                            </td>
                            <td class="font-semibold">{{ $examType->weight }}%</td>
                            <td class="text-sm text-gray-600">{{ $examType->description ?? '-' }}</td>
                            <td>
                                @if($examType->is_active)
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle mr-1"></i> Active
                                    </span>
                                @else
                                    <span class="badge badge-secondary">
                                        <i class="fas fa-pause-circle mr-1"></i> Inactive
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center space-x-2">
                                    <button type="button"
                                            onclick='editExamType(@json($examType))'
                                            class="text-blue-600 hover:text-blue-700"
                                            data-tooltip="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" action="{{ route('grades.exam-types.destroy', $examType) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Are you sure?')"
                                                class="text-red-600 hover:text-red-700"
                                                data-tooltip="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-gray-400">
                                <i class="fas fa-clipboard-list text-4xl mb-3"></i>
                                <p class="text-lg">No exam types configured</p>
                                <button type="button" onclick="openAddModal()" class="text-primary-600 hover:text-primary-700 mt-2 inline-block">
                                    Add your first exam type
                                </button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="examTypeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <form id="examTypeForm" method="POST" action="{{ route('grades.exam-types.store') }}">
                @csrf
                <div id="methodField"></div>
                
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Add Exam Type</h3>
                    <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" required class="form-input" placeholder="e.g., Continuous Assessment">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Code <span class="text-red-500">*</span></label>
                        <input type="text" name="code" id="code" required class="form-input" placeholder="e.g., ca1">
                        <p class="text-xs text-gray-500 mt-1">Unique identifier (lowercase, no spaces)</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Weight (%) <span class="text-red-500">*</span></label>
                        <input type="number" name="weight" id="weight" required min="0" max="100" class="form-input" placeholder="e.g., 20">
                        <p class="text-xs text-gray-500 mt-1">Percentage weight in final grade calculation</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" id="description" rows="3" class="form-input" placeholder="Optional description"></textarea>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <label for="is_active" class="ml-2 text-sm text-gray-700">Active</label>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 p-4 border-t">
                    <button type="button" onclick="closeModal()" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add Exam Type';
    document.getElementById('examTypeForm').action = '{{ route('grades.exam-types.store') }}';
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('examTypeForm').reset();
    document.getElementById('is_active').checked = true;
    document.getElementById('examTypeModal').classList.remove('hidden');
}

function editExamType(examType) {
    document.getElementById('modalTitle').textContent = 'Edit Exam Type';
    document.getElementById('examTypeForm').action = `/grades/exam-types/${examType.id}`;
    document.getElementById('methodField').innerHTML = '@method("PUT")';
    
    document.getElementById('name').value = examType.name;
    document.getElementById('code').value = examType.code;
    document.getElementById('weight').value = examType.weight;
    document.getElementById('description').value = examType.description || '';
    document.getElementById('is_active').checked = examType.is_active;
    
    document.getElementById('examTypeModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('examTypeModal').classList.add('hidden');
}
</script>
@endsection
