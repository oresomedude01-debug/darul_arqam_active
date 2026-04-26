@extends('layouts.spa')

@section('content')
<div class="min-h-screen bg-gray-50 px-6 py-8">
    <!-- Breadcrumb & Header -->
    <div class="mb-8">
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
            <a href="{{ route('results.index') }}" class="text-primary-600 hover:text-primary-700">Results</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span>Term Summary</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">Term Summary</h1>
        <p class="text-gray-600 mt-2">{{ $term->term }} | {{ $session->session }} | Overall Performance by Class</p>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow mb-6 p-4 border-l-4 border-primary-600">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4 flex-wrap">
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1">Search Class</label>
                    <input type="text" id="searchClass" placeholder="Search by class name..." 
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm w-64 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1">Sort By</label>
                    <select id="sortClass" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="average-high">Average (High to Low)</option>
                        <option value="average-low">Average (Low to High)</option>
                        <option value="name">Class Name (A-Z)</option>
                    </select>
                </div>
            </div>
            <button onclick="resetClassFilters()" class="px-4 py-2 bg-gray-300 text-gray-900 rounded-lg hover:bg-gray-400 transition-colors text-sm font-medium">
                <i class="fas fa-redo mr-2"></i>Reset
            </button>
        </div>
    </div>

    <!-- Summary Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Class</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Results Entered</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Average Score</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Highest</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Lowest</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($classes as $classData)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="font-medium text-gray-900">{{ $classData['class']->name }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                {{ $classData['result_count'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-bold text-lg">{{ number_format($classData['average_score'], 2) }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-semibold text-green-600">{{ number_format($classData['highest_score'], 2) }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-semibold text-red-600">{{ number_format($classData['lowest_score'], 2) }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('results.class.report', [$session, $term, $classData['class']]) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                View Details
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Back Button -->
    <div class="mt-6">
        <a href="{{ route('results.term.manage', [$session, $term]) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-900 font-medium rounded-lg hover:bg-gray-300 transition-colors gap-2">
            <i class="fas fa-arrow-left"></i>Back
        </a>
    </div>
</div>

<script>
function applyClassFilters() {
    const searchValue = document.getElementById('searchClass').value.toLowerCase();
    const sortValue = document.getElementById('sortClass').value;
    
    let rows = Array.from(document.querySelectorAll('tbody tr'));
    
    // Filter by search
    if (searchValue) {
        rows = rows.filter(row => {
            const className = row.cells[0].textContent.toLowerCase();
            return className.includes(searchValue);
        });
    }
    
    // Sort
    rows.sort((a, b) => {
        if (sortValue === 'average-high') {
            const aAvg = parseFloat(a.cells[3].textContent);
            const bAvg = parseFloat(b.cells[3].textContent);
            return bAvg - aAvg;
        } else if (sortValue === 'average-low') {
            const aAvg = parseFloat(a.cells[3].textContent);
            const bAvg = parseFloat(b.cells[3].textContent);
            return aAvg - bAvg;
        } else if (sortValue === 'name') {
            const aName = a.cells[0].textContent.toLowerCase();
            const bName = b.cells[0].textContent.toLowerCase();
            return aName.localeCompare(bName);
        }
        return 0;
    });
    
    // Clear and re-add filtered rows
    const tbody = document.querySelector('tbody');
    tbody.innerHTML = '';
    rows.forEach(row => tbody.appendChild(row));
    
    // Update counter
    updateClassRowCount(rows.length);
}

function updateClassRowCount(count) {
    let counter = document.getElementById('classRowCounter');
    if (!counter) {
        const table = document.querySelector('table');
        counter = document.createElement('p');
        counter.id = 'classRowCounter';
        counter.className = 'text-sm text-gray-600 mt-2';
        table.parentElement.appendChild(counter);
    }
    counter.textContent = `Showing ${count} class${count !== 1 ? 'es' : ''}`;
}

function resetClassFilters() {
    document.getElementById('searchClass').value = '';
    document.getElementById('sortClass').value = 'average-high';
    
    // Show all rows
    document.querySelectorAll('tbody tr').forEach(row => {
        row.style.display = '';
    });
    
    applyClassFilters();
}

// Add event listeners
document.getElementById('searchClass').addEventListener('keyup', applyClassFilters);
document.getElementById('sortClass').addEventListener('change', applyClassFilters);

// Initial row count
updateClassRowCount(document.querySelectorAll('tbody tr').length);
</script>
@endsection
