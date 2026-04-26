@extends('layouts.spa')

@section('title', 'Grade Scale Setup')

@section('breadcrumb')
    <span class="text-gray-400">Grades</span>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Grade Scale Setup</span>
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
            <h1 class="text-2xl font-bold text-gray-900">Grade Scale Setup</h1>
            <p class="text-gray-600 mt-1">Configure grade ranges and remarks</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('grades.exam-types') }}" class="btn btn-outline">
                <i class="fas fa-clipboard-list mr-2"></i>
                Exam Types
            </a>
            <button type="button" onclick="openAddModal()" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>
                Add Grade Scale
            </button>
        </div>
    </div>

    <!-- Grade Scales Table -->
    <div class="card">
        <div class="card-header">
            <h2 class="text-lg font-semibold text-gray-900">Grade Scales</h2>
        </div>
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Grade</th>
                            <th>Score Range</th>
                            <th>Remark</th>
                            <th>Passing?</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gradeScales as $scale)
                        <tr>
                            <td>
                                <span class="text-2xl font-bold" style="color: {{ $scale->color ?? '#000' }}">
                                    {{ $scale->grade }}
                                </span>
                            </td>
                            <td class="font-medium">{{ $scale->min_score }} - {{ $scale->max_score }}</td>
                            <td>{{ $scale->remark }}</td>
                            <td>
                                @if($scale->is_passing)
                                    <span class="badge badge-success">
                                        <i class="fas fa-check mr-1"></i> Passing
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        <i class="fas fa-times mr-1"></i> Failing
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center space-x-2">
                                    <button type="button"
                                            onclick='editScale(@json($scale))'
                                            class="text-blue-600 hover:text-blue-700"
                                            data-tooltip="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" action="{{ route('grades.scales.destroy', $scale) }}" class="inline">
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
                            <td colspan="5" class="text-center py-8 text-gray-400">
                                <i class="fas fa-list text-4xl mb-3"></i>
                                <p class="text-lg">No grade scales configured</p>
                                <button type="button" onclick="openAddModal()" class="text-primary-600 hover:text-primary-700 mt-2 inline-block">
                                    Add your first grade scale
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
<div id="gradeScaleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <form id="gradeScaleForm" method="POST" action="{{ route('grades.scales.store') }}">
                @csrf
                <div id="methodField"></div>
                
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Add Grade Scale</h3>
                    <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Grade <span class="text-red-500">*</span></label>
                        <input type="text" name="grade" id="grade" required class="form-input" placeholder="A, B, C, etc.">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Min Score <span class="text-red-500">*</span></label>
                            <input type="number" name="min_score" id="min_score" required min="0" max="100" step="0.01" class="form-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Max Score <span class="text-red-500">*</span></label>
                            <input type="number" name="max_score" id="max_score" required min="0" max="100" step="0.01" class="form-input">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Remark</label>
                        <input type="text" name="remark" id="remark" class="form-input" placeholder="Excellent, Good, etc.">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Color (Optional)</label>
                        <input type="color" name="color" id="color" class="form-input h-10">
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_passing" id="is_passing" value="1" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <label for="is_passing" class="ml-2 text-sm text-gray-700">This is a passing grade</label>
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
    document.getElementById('modalTitle').textContent = 'Add Grade Scale';
    document.getElementById('gradeScaleForm').action = '{{ route('grades.scales.store') }}';
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('gradeScaleForm').reset();
    document.getElementById('gradeScaleModal').classList.remove('hidden');
}

function editScale(scale) {
    document.getElementById('modalTitle').textContent = 'Edit Grade Scale';
    document.getElementById('gradeScaleForm').action = `/grades/scales/${scale.id}`;
    document.getElementById('methodField').innerHTML = '@method("PUT")';
    
    document.getElementById('grade').value = scale.grade;
    document.getElementById('min_score').value = scale.min_score;
    document.getElementById('max_score').value = scale.max_score;
    document.getElementById('remark').value = scale.remark || '';
    document.getElementById('color').value = scale.color || '#000000';
    document.getElementById('is_passing').checked = scale.is_passing;
    
    document.getElementById('gradeScaleModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('gradeScaleModal').classList.add('hidden');
}
</script>
@endsection
