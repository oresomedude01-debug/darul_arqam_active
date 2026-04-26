@extends('layouts.spa')

@section('title', 'Generate Tokens')

@section('breadcrumb')
    <span class="text-gray-400">Student Management</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('tokens.index') }}" class="text-gray-400 hover:text-gray-600">Registration Tokens</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Generate New</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Generate Registration Tokens</h1>
            <p class="text-gray-600 mt-1">Create new enrollment tokens for prospective students</p>
        </div>
        <a href="{{ route('tokens.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to List
        </a>
    </div>

    <!-- Info Alert -->
    <div class="alert alert-info">
        <i class="fas fa-info-circle text-xl"></i>
        <div>
            <p class="font-medium">About Registration Tokens</p>
            <p class="text-sm mt-1">Registration tokens are unique codes that allow parents to enroll their children. Each token can only be used once and can have an optional expiry date.</p>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('tokens.store') }}" method="POST" x-data="tokenGenerator()">
        @csrf

        <!-- Token Configuration -->
        <div class="card">
            <div class="card-header">
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-cog mr-2 text-primary-600"></i>
                    Token Configuration
                </h2>
            </div>
            <div class="card-body space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Number of Tokens -->
                    <div class="md:col-span-2">
                        <label class="form-label">
                            Number of Tokens to Generate <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center space-x-4">
                            <input type="range"
                                   name="quantity"
                                   x-model="quantity"
                                   min="1"
                                   max="100"
                                   class="flex-1">
                            <div class="flex items-center space-x-2">
                                <input type="number"
                                       x-model="quantity"
                                       name="quantity"
                                       min="1"
                                       max="100"
                                       required
                                       class="form-input w-24 text-center">
                                <span class="text-gray-600">token(s)</span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">
                            You can generate between 1 and 100 tokens at once.
                        </p>
                    </div>

                    <!-- Session/Academic Year -->
                    <div>
                        <label class="form-label">
                            Session/Academic Year <span class="text-red-500">*</span>
                        </label>
                        <select name="session_year" class="form-select" required>
                            <option value="">Select Session</option>
                            @foreach($academicSessions as $session)
                                <option value="{{ $session->session }}">{{ $session->session }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Academic year for enrollment</p>
                    </div>

                    <!-- Class Level -->
                    <div>
                        <label class="form-label">
                            Class/Level (Optional)
                        </label>
                        <select name="class_level" class="form-select">
                            <option value="">Any Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Restrict token to specific class (optional)</p>
                    </div>

                    <!-- Expiry Date -->
                    <div>
                        <label class="form-label">
                            Expiry Date (Optional)
                        </label>
                        <input type="date"
                               name="expires_at"
                               x-model="expiresAt"
                               :min="minDate"
                               class="form-input">
                        <p class="text-xs text-gray-500 mt-1">Leave blank for no expiry</p>
                    </div>

                    <!-- Quick Expiry Options -->
                    <div>
                        <label class="form-label">Quick Expiry</label>
                        <div class="flex flex-wrap gap-2">
                            <button type="button"
                                    @click="setExpiry(7)"
                                    class="btn btn-sm btn-outline">
                                7 Days
                            </button>
                            <button type="button"
                                    @click="setExpiry(30)"
                                    class="btn btn-sm btn-outline">
                                30 Days
                            </button>
                            <button type="button"
                                    @click="setExpiry(60)"
                                    class="btn btn-sm btn-outline">
                                60 Days
                            </button>
                            <button type="button"
                                    @click="setExpiry(90)"
                                    class="btn btn-sm btn-outline">
                                90 Days
                            </button>
                        </div>
                    </div>

                    <!-- Note/Description -->
                    <div class="md:col-span-2">
                        <label class="form-label">
                            Note/Description (Optional)
                        </label>
                        <textarea name="note"
                                  class="form-textarea"
                                  rows="3"
                                  placeholder="e.g., For PTA recommendations, Staff children, etc."
                                  maxlength="500"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Add a note to help identify the purpose of these tokens</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview -->
        <div class="card" x-show="quantity > 0">
            <div class="card-header">
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-eye mr-2 text-primary-600"></i>
                    Generation Preview
                </h2>
            </div>
            <div class="card-body">
                <div class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-lg p-6">
                    <div class="text-center">
                        <div class="text-6xl font-bold text-primary-600" x-text="quantity"></div>
                        <p class="text-primary-800 mt-2 font-medium">
                            <span x-text="quantity === 1 ? 'Token' : 'Tokens'"></span> will be generated
                        </p>
                        <div class="mt-4 text-sm text-primary-700 space-y-1">
                            <p>Format: <code class="bg-white px-2 py-1 rounded">DAREG-YYYYMM-XXXXX</code></p>
                            <p>Example: <code class="bg-white px-2 py-1 rounded">DAREG-202511-A1B2C</code></p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-start space-x-3 p-4 bg-gray-50 rounded-lg">
                        <i class="fas fa-calendar text-primary-600 mt-1"></i>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Session Year</p>
                            <p class="text-sm text-gray-600 mt-1">Will be set based on selection</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3 p-4 bg-gray-50 rounded-lg">
                        <i class="fas fa-clock text-primary-600 mt-1"></i>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Expiry</p>
                            <p class="text-sm text-gray-600 mt-1" x-text="expiresAt || 'No expiry set'"></p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3 p-4 bg-gray-50 rounded-lg">
                        <i class="fas fa-check-circle text-green-600 mt-1"></i>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Initial Status</p>
                            <p class="text-sm text-gray-600 mt-1">All tokens will be <span class="badge badge-success">Active</span></p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3 p-4 bg-gray-50 rounded-lg">
                        <i class="fas fa-lock text-primary-600 mt-1"></i>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Usage</p>
                            <p class="text-sm text-gray-600 mt-1">Single-use per token</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-4">
            <a href="{{ route('tokens.index') }}" class="btn btn-outline">
                Cancel
            </a>
            <button type="reset" class="btn btn-secondary">
                <i class="fas fa-redo mr-2"></i>
                Reset
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-magic mr-2"></i>
                Generate <span x-text="quantity"></span> Token(s)
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function tokenGenerator() {
    return {
        quantity: 1,
        expiresAt: '',
        minDate: new Date().toISOString().split('T')[0],

        setExpiry(days) {
            const date = new Date();
            date.setDate(date.getDate() + days);
            this.expiresAt = date.toISOString().split('T')[0];
        }
    }
}
</script>
@endpush
@endsection
