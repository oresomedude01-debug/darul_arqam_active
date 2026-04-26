@extends('layouts.spa')

@section('content')
<div class="min-h-screen bg-gray-50 px-2 sm:px-3 md:px-4 lg:px-6 py-4 sm:py-6 md:py-8">
    <!-- Breadcrumb & Header -->
    <div class="mb-8">
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
            <a href="{{ route('results.index') }}" class="text-primary-600 hover:text-primary-700">Results</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('results.session.show', $session) }}" class="text-primary-600 hover:text-primary-700">{{ $session->session }}</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span>Report</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">Class Report Card</h1>
        <p class="text-gray-600 mt-2">{{ $class->name }} | {{ $term->term }} | {{ $session->session }}</p>
    </div>

    <!-- Class Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-4 mb-6 md:mb-8">
        <div class="bg-white rounded-lg shadow p-3 md:p-4">
            <p class="text-gray-600 text-xs md:text-sm">Class Average</p>
            <p class="text-xl md:text-2xl font-bold text-gray-900">{{ number_format($classStats['class_average'], 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-3 md:p-4">
            <p class="text-gray-600 text-xs md:text-sm">Total Students</p>
            <p class="text-xl md:text-2xl font-bold text-gray-900">{{ $classStats['total_students'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-3 md:p-4">
            <p class="text-gray-600 text-xs md:text-sm">Avg Passed</p>
            <p class="text-xl md:text-2xl font-bold text-green-600">{{ number_format($classStats['average_passed'], 1) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-3 md:p-4">
            <p class="text-gray-600 text-xs md:text-sm">Avg Failed</p>
            <p class="text-xl md:text-2xl font-bold text-red-600">{{ number_format($classStats['average_failed'], 1) }}</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow mb-6 p-3 md:p-4 border-l-4 border-primary-600">
        <div class="flex items-center justify-between flex-wrap gap-3 md:gap-4">
            <div class="flex items-center gap-3 md:gap-4 flex-wrap w-full md:w-auto">
                <div class="flex-1 md:flex-none">
                    <label class="text-xs md:text-sm font-medium text-gray-700 block mb-1">Search</label>
                    <input type="text" id="searchInput" placeholder="Student name or no..." 
                        class="w-full md:w-64 px-3 py-2 border border-gray-300 rounded-lg text-xs md:text-sm focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="flex-1 md:flex-none">
                    <label class="text-xs md:text-sm font-medium text-gray-700 block mb-1">Status</label>
                    <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs md:text-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All</option>
                        <option value="passed">Passed</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div class="flex-1 md:flex-none">
                    <label class="text-xs md:text-sm font-medium text-gray-700 block mb-1">Sort</label>
                    <select id="sortFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs md:text-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="position">Position</option>
                        <option value="average-asc">Average</option>
                        <option value="name">Name</option>
                    </select>
                </div>
            </div>
            <button onclick="resetFilters()" class="w-full md:w-auto px-3 md:px-4 py-2 bg-gray-300 text-gray-900 rounded-lg hover:bg-gray-400 transition-colors text-xs md:text-sm font-medium">
                <i class="fas fa-redo mr-2"></i>Reset
            </button>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Desktop Table View (lg+) -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">No.</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Student Name</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Admission No.</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Average</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Subjects Taken</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Passed</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Failed</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Pass %</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($reportData as $position => $data)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary-100 text-primary-700 font-semibold text-sm">
                                    {{ $position + 1 }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-user text-gray-600 text-xs"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-medium text-gray-900">{{ $data['student']->first_name }} {{ $data['student']->last_name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $data['student']->admission_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="font-semibold text-gray-900">{{ number_format($data['average'], 2) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                {{ $data['subjects_taken'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    {{ $data['passed'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                    {{ $data['failed'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center font-semibold">
                                {{ $data['pass_percentage'] }}%
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <a href="{{ route('results.student.card', [$session, $term, $data['student']]) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                    View Card
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (hidden on lg+) -->
        <div class="lg:hidden space-y-3 p-2 sm:p-3 md:p-4">
            @foreach ($reportData as $position => $data)
                <div class="bg-white border border-gray-200 rounded-lg p-3 md:p-4 shadow-sm">
                    <!-- Position & Student Info -->
                    <div class="flex items-start gap-3 mb-4 pb-4 border-b border-gray-200">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full bg-primary-100 text-primary-700 font-semibold text-sm flex-shrink-0">
                            {{ $position + 1 }}
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-gray-900">{{ $data['student']->first_name }} {{ $data['student']->last_name }}</p>
                            <p class="text-xs text-gray-600">{{ $data['student']->admission_number }}</p>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="bg-gray-50 rounded p-3 text-center">
                            <p class="text-xs text-gray-600 mb-1">Average</p>
                            <p class="font-bold text-lg text-gray-900">{{ number_format($data['average'], 2) }}</p>
                        </div>
                        <div class="bg-gray-50 rounded p-3 text-center">
                            <p class="text-xs text-gray-600 mb-1">Subjects</p>
                            <p class="font-bold text-lg text-gray-900">{{ $data['subjects_taken'] }}</p>
                        </div>
                    </div>

                    <!-- Performance Stats -->
                    <div class="grid grid-cols-3 gap-2 mb-4">
                        <div class="text-center">
                            <p class="text-xs text-gray-600 mb-1">Passed</p>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                {{ $data['passed'] }}
                            </span>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-600 mb-1">Failed</p>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                {{ $data['failed'] }}
                            </span>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-600 mb-1">Pass %</p>
                            <p class="font-bold text-gray-900">{{ $data['pass_percentage'] }}%</p>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <a href="{{ route('results.student.card', [$session, $term, $data['student']]) }}" class="block w-full text-center px-3 py-2 bg-primary-600 text-white text-xs md:text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                        View Card
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-6">
        <a href="{{ route('results.term.manage', [$session, $term]) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-900 font-medium rounded-lg hover:bg-gray-300 transition-colors gap-2">
            <i class="fas fa-arrow-left"></i>Back
        </a>
    </div>
</div>

<script>
function applyFilters() {
    const searchValue = document.getElementById('searchInput').value.toLowerCase();
    const statusValue = document.getElementById('statusFilter').value;
    const sortValue = document.getElementById('sortFilter').value;
    
    let rows = Array.from(document.querySelectorAll('tbody tr'));
    
    // Filter by search
    if (searchValue) {
        rows = rows.filter(row => {
            const studentName = row.cells[1].textContent.toLowerCase();
            const admissionNo = row.cells[2].textContent.toLowerCase();
            return studentName.includes(searchValue) || admissionNo.includes(searchValue);
        });
    }
    
    // Filter by status
    if (statusValue) {
        rows = rows.filter(row => {
            const passPercentage = parseInt(row.cells[8].textContent);
            return statusValue === 'passed' ? passPercentage === 100 : passPercentage < 100;
        });
    }
    
    // Sort
    rows.sort((a, b) => {
        if (sortValue === 'position') {
            const aPos = parseInt(a.cells[0].textContent);
            const bPos = parseInt(b.cells[0].textContent);
            return aPos - bPos;
        } else if (sortValue === 'average-asc') {
            const aAvg = parseFloat(a.cells[3].textContent);
            const bAvg = parseFloat(b.cells[3].textContent);
            return bAvg - aAvg;
        } else if (sortValue === 'name') {
            const aName = a.cells[1].textContent.toLowerCase();
            const bName = b.cells[1].textContent.toLowerCase();
            return aName.localeCompare(bName);
        }
        return 0;
    });
    
    // Clear and re-add filtered rows
    const tbody = document.querySelector('tbody');
    tbody.innerHTML = '';
    rows.forEach(row => tbody.appendChild(row));
    
    // Update counter
    updateRowCount(rows.length);
}

function updateRowCount(count) {
    let counter = document.getElementById('rowCounter');
    if (!counter) {
        const table = document.querySelector('table');
        counter = document.createElement('p');
        counter.id = 'rowCounter';
        counter.className = 'text-sm text-gray-600 mt-2';
        table.parentElement.appendChild(counter);
    }
    counter.textContent = `Showing ${count} student${count !== 1 ? 's' : ''}`;
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('sortFilter').value = 'position';
    
    // Show all rows
    document.querySelectorAll('tbody tr').forEach(row => {
        row.style.display = '';
    });
    
    applyFilters();
}

// Add event listeners
document.getElementById('searchInput').addEventListener('keyup', applyFilters);
document.getElementById('statusFilter').addEventListener('change', applyFilters);
document.getElementById('sortFilter').addEventListener('change', applyFilters);

// Initial row count
updateRowCount(document.querySelectorAll('tbody tr').length);
</script>
@endsection
