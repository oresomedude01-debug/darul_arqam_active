@extends('layouts.spa')

@section('title', 'Edit Grading Settings')

@section('breadcrumb')
    <span class="text-gray-400">Settings</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('settings.school.index') }}" class="text-primary-600 hover:text-primary-700">School Settings</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Grading Settings</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Grading Settings</h1>
            <p class="text-sm text-gray-600 mt-1">Configure grade scales, passing scores, and scoring weights</p>
        </div>
        <a href="{{ route('settings.school.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left mr-2"></i>Back to Settings
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('settings.school.update-grading') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Scoring Configuration -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-percentage mr-2 text-primary-600"></i>Scoring Configuration
                </h3>
            </div>
            <div class="card-body space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="form-group">
                        <label class="form-label">Continuous Assessment (CA) Weight <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="ca_weight" value="{{ old('ca_weight', $settings->ca_weight) }}" class="form-input @error('ca_weight') border-red-500 @enderror" min="0" max="100" required>
                            <span class="absolute right-3 top-3 text-gray-500">%</span>
                        </div>
                        @error('ca_weight')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Exam Weight <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="exam_weight" value="{{ old('exam_weight', $settings->exam_weight) }}" class="form-input @error('exam_weight') border-red-500 @enderror" min="0" max="100" required>
                            <span class="absolute right-3 top-3 text-gray-500">%</span>
                        </div>
                        @error('exam_weight')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Total Weight</label>
                        <div class="relative">
                            <input type="number" id="total-weight" value="100" class="form-input bg-gray-50 text-gray-600" disabled>
                            <span class="absolute right-3 top-3 text-gray-500">%</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">CA + Exam must equal 100%</p>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Example:</strong> If CA Weight is 30% and Exam Weight is 70%, the final score will be calculated as: <code class="bg-gray-100 px-2 py-1 rounded">Final = (CA × 0.30) + (Exam × 0.70)</code>
                </div>
            </div>
        </div>

        <!-- Passing Score -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-check-circle mr-2 text-primary-600"></i>Passing Score
                </h3>
            </div>
            <div class="card-body space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Minimum Passing Score <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="passing_score" value="{{ old('passing_score', $settings->passing_score) }}" class="form-input @error('passing_score') border-red-500 @enderror" min="0" max="100" required>
                            <span class="absolute right-3 top-3 text-gray-500">%</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Scores below this are considered failing</p>
                        @error('passing_score')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Grade Boundaries -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-list mr-2 text-primary-600"></i>Grade Boundaries
                </h3>
            </div>
            <div class="card-body">
                <p class="text-sm text-gray-600 mb-4">Define the score ranges for each grade. Enter the minimum score for each grade in ascending order.</p>

                @php
                    $boundaries = $settings->grade_boundaries ?? [
                        'A' => 80,
                        'B' => 70,
                        'C' => 60,
                        'D' => 50,
                        'E' => 40,
                        'F' => 0
                    ];
                @endphp

                <div class="space-y-3">
                    @foreach(['A', 'B', 'C', 'D', 'E', 'F'] as $grade)
                        <div class="flex items-end gap-4">
                            <div class="flex-1 form-group">
                                <label class="form-label">Grade <span class="font-bold text-lg text-primary-600">{{ $grade }}</span> - Minimum Score <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="number" name="grade_boundaries[{{ $grade }}]" value="{{ old('grade_boundaries.' . $grade, $boundaries[$grade] ?? 0) }}" class="form-input @error('grade_boundaries.' . $grade) border-red-500 @enderror" min="0" max="100" required>
                                    <span class="absolute right-3 top-3 text-gray-500">%</span>
                                </div>
                                @error('grade_boundaries.' . $grade)
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="alert alert-warning mt-6">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Important:</strong> Ensure grade boundaries are in descending order (A > B > C > D > E > F). Grade F should typically be 0.
                </div>

                <!-- Grade Preview -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="font-semibold text-gray-900 mb-4">Grade Scale Preview</h4>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach(['A' => 'success', 'B' => 'info', 'C' => 'warning', 'D' => 'warning', 'E' => 'danger', 'F' => 'danger'] as $grade => $color)
                            @php
                                $minScore = $boundaries[$grade] ?? 0;
                                $nextGrade = array_search($grade, ['A', 'B', 'C', 'D', 'E', 'F']);
                                $maxScore = $nextGrade > 0 ? $boundaries[array_keys($boundaries)[$nextGrade - 1]] - 1 : 100;
                            @endphp
                            <div class="bg-{{ $color }}-50 border border-{{ $color }}-200 rounded-lg p-3 text-center">
                                <div class="text-3xl font-bold text-{{ $color }}-600">{{ $grade }}</div>
                                <div class="text-sm text-{{ $color }}-700 mt-1">{{ $minScore }}% - {{ $maxScore }}%</div>
                            </div>
                        @endforeach
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
        const caInput = document.querySelector('input[name="ca_weight"]');
        const examInput = document.querySelector('input[name="exam_weight"]');
        const totalDisplay = document.getElementById('total-weight');

        function updateTotal() {
            const ca = parseInt(caInput.value || 0);
            const exam = parseInt(examInput.value || 0);
            const total = ca + exam;
            totalDisplay.value = total;
            
            // Visual feedback
            if (total === 100) {
                totalDisplay.classList.remove('bg-red-50', 'border-red-300');
                totalDisplay.classList.add('bg-green-50', 'border-green-300');
            } else if (total > 100) {
                totalDisplay.classList.remove('bg-green-50', 'border-green-300');
                totalDisplay.classList.add('bg-red-50', 'border-red-300');
            } else {
                totalDisplay.classList.remove('bg-green-50', 'border-green-300');
                totalDisplay.classList.add('bg-yellow-50', 'border-yellow-300');
            }
        }

        caInput.addEventListener('input', updateTotal);
        examInput.addEventListener('input', updateTotal);
        updateTotal();
    });
</script>
@endsection
