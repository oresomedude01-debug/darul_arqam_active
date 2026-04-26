@extends('layouts.spa')

@section('content')
<div class="min-h-screen bg-gray-50 px-4 sm:px-6 py-6 sm:py-8">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs sm:text-sm text-gray-600 mb-6 overflow-x-auto pb-2">
        <a href="{{ route('dashboard') }}" class="text-primary-600 hover:text-primary-700 whitespace-nowrap">Home</a>
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-900 font-medium whitespace-nowrap">Results</span>
    </div>

    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Results Management</h1>
            <p class="text-sm sm:text-base text-gray-600 mt-2">Manage academic results by session and term</p>
        </div>
        <div>
            <a href="{{ route('results.print.selection') }}" class="inline-flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-blue-600 text-white font-medium text-sm sm:text-base rounded-lg hover:bg-blue-700 transition-colors gap-2 whitespace-nowrap">
                <i class="fas fa-print"></i><span class="hidden sm:inline">Print Result Card</span><span class="sm:hidden">Print</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats (KPI) -->
    @if ($sessions->count() > 0)
        <div class="mb-8 hidden sm:grid sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-xs sm:text-sm font-medium">Total Sessions</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2">{{ $sessions->count() }}</p>
                    </div>
                    <div class="text-3xl sm:text-4xl text-primary-100 flex-shrink-0">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-xs sm:text-sm font-medium">Active Sessions</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2">{{ $sessions->where('is_active', true)->count() }}</p>
                    </div>
                    <div class="text-3xl sm:text-4xl text-green-100 flex-shrink-0">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-xs sm:text-sm font-medium">Total Terms</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2">{{ count($termStats) }}</p>
                    </div>
                    <div class="text-3xl sm:text-4xl text-blue-100 flex-shrink-0">
                        <i class="fas fa-bookmark"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-xs sm:text-sm font-medium">Results Entered</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2">{{ is_array($termStats) ? array_sum(array_column($termStats, 'result_count')) : $termStats->sum('result_count') }}</p>
                    </div>
                    <div class="text-3xl sm:text-4xl text-purple-100 flex-shrink-0">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Alert Messages -->
    @if ($errors->any())
        <div class="mb-4 p-3 sm:p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-xs sm:text-sm text-red-700 font-medium">{{ $errors->first() }}</p>
        </div>
    @endif

    @if (session('success'))
        <div class="mb-4 p-3 sm:p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-xs sm:text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Search & Filter Section -->
    <div class="bg-white rounded-lg shadow mb-6 p-3 sm:p-4 border-l-4 border-primary-600">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:gap-4">
            <div class="flex-1">
                <label class="text-xs sm:text-sm font-medium text-gray-700 block mb-1">Search Session</label>
                <input type="text" id="searchInput" placeholder="Search by name..." 
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div class="w-full sm:w-auto">
                <label class="text-xs sm:text-sm font-medium text-gray-700 block mb-1">Filter by Status</label>
                <select id="statusFilter" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All Sessions</option>
                    <option value="active">Active Only</option>
                    <option value="inactive">Inactive Only</option>
                </select>
            </div>
            <div class="w-full sm:w-auto">
                <label class="text-xs sm:text-sm font-medium text-gray-700 block mb-1">Sort By</label>
                <select id="sortFilter" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    <option value="latest">Latest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="results-high">Most Results</option>
                    <option value="results-low">Least Results</option>
                </select>
            </div>
            <button onclick="resetFilters()" class="w-full sm:w-auto px-4 py-2 bg-gray-300 text-gray-900 rounded-lg hover:bg-gray-400 transition-colors text-sm font-medium">
                <i class="fas fa-redo mr-1 sm:mr-2"></i><span class="hidden sm:inline">Reset</span><span class="sm:hidden">Clear</span>
            </button>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Desktop Table View -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Session</th>
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Description</th>
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider whitespace-nowrap">Status</th>
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider whitespace-nowrap">Terms</th>
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider whitespace-nowrap">Results</th>
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($sessions as $session)
                        @php
                            $sessionTerms = is_array($termStats) ? array_filter($termStats, fn($stat) => $stat['term']->academic_session_id == $session->id) : $termStats->filter(fn($stat) => $stat['term']->academic_session_id == $session->id);
                            $totalResults = is_array($sessionTerms) ? array_sum(array_column($sessionTerms, 'result_count')) : $sessionTerms->sum('result_count');
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                <p class="font-semibold text-xs sm:text-sm text-gray-900">{{ $session->session }}</p>
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4">
                                <p class="text-xs sm:text-sm text-gray-600">{{ $session->description ?? 'Academic Session' }}</p>
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-semibold {{ $session->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $session->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center px-2 sm:px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                    {{ count($sessionTerms) }}
                                </span>
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center px-2 sm:px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                    {{ $totalResults }}
                                </span>
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-center">
                                <a href="{{ route('results.session.show', $session) }}" class="inline-flex items-center px-2 sm:px-4 py-1 sm:py-2 bg-primary-600 text-white text-xs font-semibold rounded-lg hover:bg-primary-700 transition-colors gap-1">
                                    <i class="fas fa-arrow-right hidden sm:inline"></i><span class="text-xs">Manage</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center">
                                <div class="text-yellow-800 bg-yellow-50 border border-yellow-200 rounded-lg p-4 inline-block">
                                    <p class="text-xs sm:text-sm"><i class="fas fa-info-circle mr-2"></i>No academic sessions found. Please create one in School Settings first.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card Grid View -->
        <div class="sm:hidden">
            @forelse ($sessions as $session)
                @php
                    $sessionTerms = is_array($termStats) ? array_filter($termStats, fn($stat) => $stat['term']->academic_session_id == $session->id) : $termStats->filter(fn($stat) => $stat['term']->academic_session_id == $session->id);
                    $totalResults = is_array($sessionTerms) ? array_sum(array_column($sessionTerms, 'result_count')) : $sessionTerms->sum('result_count');
                @endphp
                <div class="p-4 border-b border-gray-200 last:border-b-0">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <p class="font-bold text-lg text-gray-900">{{ $session->session }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ $session->description ?? 'Academic Session' }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $session->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $session->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-2 mb-4">
                        <div class="bg-blue-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-600 font-medium">Terms</p>
                            <p class="text-lg font-bold text-blue-800 mt-1">{{ count($sessionTerms) }}</p>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-600 font-medium">Results</p>
                            <p class="text-lg font-bold text-purple-800 mt-1">{{ $totalResults }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-600 font-medium">Status</p>
                            <p class="text-lg font-bold text-gray-800 mt-1">{{ $session->is_active ? '✓' : '✗' }}</p>
                        </div>
                    </div>

                    <a href="{{ route('results.session.show', $session) }}" class="w-full block text-center px-4 py-2 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-700 transition-colors">
                        Manage
                    </a>
                </div>
            @empty
                <div class="px-4 py-8 text-center">
                    <div class="text-yellow-800 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-sm"><i class="fas fa-info-circle mr-2"></i>No academic sessions found. Please create one in School Settings first.</p>
                    </div>
                </div>
            @endforelse
        </div>
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
            const session = row.cells[0].textContent.toLowerCase();
            const description = row.cells[1].textContent.toLowerCase();
            return session.includes(searchValue) || description.includes(searchValue);
        });
    }
    
    // Filter by status
    if (statusValue) {
        rows = rows.filter(row => {
            const status = row.cells[2].textContent.toLowerCase();
            return statusValue === 'active' ? status.includes('active') : status.includes('inactive');
        });
    }
    
    // Sort
    rows.sort((a, b) => {
        if (sortValue === 'latest') {
            return 0; // Keep original order
        } else if (sortValue === 'oldest') {
            return 0; // Keep original order reversed
        } else if (sortValue === 'results-high') {
            const aResults = parseInt(a.cells[4].textContent);
            const bResults = parseInt(b.cells[4].textContent);
            return bResults - aResults;
        } else if (sortValue === 'results-low') {
            const aResults = parseInt(a.cells[4].textContent);
            const bResults = parseInt(b.cells[4].textContent);
            return aResults - bResults;
        }
        return 0;
    });
    
    // Clear and re-add filtered rows
    const tbody = document.querySelector('tbody');
    tbody.innerHTML = '';
    
    if (rows.length === 0) {
        const tr = document.createElement('tr');
        tr.innerHTML = '<td colspan="6" class="px-6 py-8 text-center text-gray-500">No sessions found matching your criteria</td>';
        tbody.appendChild(tr);
    } else {
        rows.forEach(row => tbody.appendChild(row));
    }
    
    // Update counter
    updateRowCount(rows.length);
}

function updateRowCount(count) {
    let counter = document.getElementById('rowCounter');
    if (!counter) {
        const table = document.querySelector('table');
        counter = document.createElement('p');
        counter.id = 'rowCounter';
        counter.className = 'text-sm text-gray-600 mt-4 text-center';
        table.parentElement.appendChild(counter);
    }
    counter.textContent = `Showing ${count} session${count !== 1 ? 's' : ''}`;
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('sortFilter').value = 'latest';
    
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
