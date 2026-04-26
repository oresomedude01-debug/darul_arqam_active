@extends('layouts.spa')

@section('title', 'Import Students')

@section('breadcrumb')
    <span class="text-gray-400">Students</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('students.index') }}" class="text-gray-400 hover:text-gray-600">All Students</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Import Students</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Import Students</h1>
            <p class="text-gray-600 mt-1">Upload a CSV file to import multiple students at once</p>
        </div>
        <a href="{{ route('students.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to List
        </a>
    </div>

    <!-- Instructions Card -->
    <div class="card bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500">
        <div class="card-body">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-600 text-3xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Import Instructions</h3>
                    <ol class="list-decimal list-inside space-y-2 text-gray-700">
                        <li>Download the CSV template file using the button below</li>
                        <li>Fill in the student information following the template format</li>
                        <li>Required fields are marked with asterisk (*): First Name, Last Name, Class Level</li>
                        <li>Date format should be: YYYY-MM-DD (e.g., 2010-01-15)</li>
                        <li>Gender should be either: male or female</li>
                        <li>Status should be: active, pending, or inactive</li>
                        <li>Upload the completed CSV file using the form below</li>
                    </ol>

                    <div class="mt-4">
                        <a href="{{ route('students.template') }}" class="btn btn-primary">
                            <i class="fas fa-download mr-2"></i>
                            Download CSV Template
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Form -->
    <div class="card">
        <div class="card-header">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-file-upload mr-2 text-primary-600"></i>
                Upload CSV File
            </h2>
        </div>
        <div class="card-body">
            <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- File Upload -->
                <div x-data="{ fileName: '' }">
                    <label class="form-label">CSV File <span class="text-red-500">*</span></label>

                    <div class="mt-2 flex items-center space-x-4">
                        <label for="csv-upload" class="relative cursor-pointer">
                            <div class="flex items-center justify-center w-full px-6 py-4 border-2 border-gray-300 border-dashed rounded-lg hover:border-primary-500 transition-colors bg-gray-50 hover:bg-primary-50">
                                <div class="text-center">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                    <p class="text-sm text-gray-600">
                                        <span class="font-semibold text-primary-600">Click to upload</span>
                                        or drag and drop
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">CSV or TXT file (max 10MB)</p>
                                </div>
                            </div>
                            <input type="file"
                                   id="csv-upload"
                                   name="csv_file"
                                   accept=".csv,.txt"
                                   class="hidden"
                                   required
                                   @change="fileName = $event.target.files[0]?.name || ''">
                        </label>
                    </div>

                    <div x-show="fileName" class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-file-csv text-green-600"></i>
                            <span class="text-sm text-gray-700">Selected file:</span>
                            <span class="text-sm font-medium text-gray-900" x-text="fileName"></span>
                        </div>
                    </div>

                    @error('csv_file')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Important Notes -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mt-0.5"></i>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Important Notes</h4>
                            <ul class="text-sm text-gray-700 space-y-1">
                                <li>• Admission numbers will be auto-generated for new students</li>
                                <li>• Empty rows will be automatically skipped</li>
                                <li>• Invalid data will be reported after import</li>
                                <li>• The first row (header) will be ignored</li>
                                <li>• Make sure all dates are in YYYY-MM-DD format</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <a href="{{ route('students.index') }}" class="btn btn-outline">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload mr-2"></i>
                        Import Students
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sample Data Preview -->
    <div class="card">
        <div class="card-header">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-table mr-2 text-primary-600"></i>
                Sample CSV Format
            </h2>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">First Name *</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Name *</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">DOB</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gender</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class *</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900">Ahmed</td>
                            <td class="px-4 py-3 text-sm text-gray-900">Ibrahim</td>
                            <td class="px-4 py-3 text-sm text-gray-600">2010-01-15</td>
                            <td class="px-4 py-3 text-sm text-gray-600">male</td>
                            <td class="px-4 py-3 text-sm text-gray-900">Primary 5</td>
                            <td class="px-4 py-3 text-sm text-gray-600">active</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">Fatima</td>
                            <td class="px-4 py-3 text-sm text-gray-900">Hassan</td>
                            <td class="px-4 py-3 text-sm text-gray-600">2012-05-20</td>
                            <td class="px-4 py-3 text-sm text-gray-600">female</td>
                            <td class="px-4 py-3 text-sm text-gray-900">Primary 3</td>
                            <td class="px-4 py-3 text-sm text-gray-600">active</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="text-xs text-gray-500 mt-4">
                <i class="fas fa-info-circle mr-1"></i>
                This is a simplified preview. The actual template includes all available fields.
            </p>
        </div>
    </div>
</div>
@endsection
