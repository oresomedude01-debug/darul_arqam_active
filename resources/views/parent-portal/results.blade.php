@extends('layouts.spa')

@section('title', 'Children Results')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Children's Academic Results</h1>
            <p class="mt-2 text-sm text-gray-600">View and print your children's grades and results</p>
        </div>

        <!-- Child & Filter Selection -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <form id="resultsForm" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Child</label>
                    <select id="studentSelect" name="student" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Select a child --</option>
                        @foreach($children as $child)
                            <option value="{{ $child->id }}" @if($selectedChild && $selectedChild->id == $child->id) selected @endif>
                                {{ $child->first_name }} {{ $child->last_name }} ({{ $child->schoolClass->name ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="sessionContainer" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Academic Session</label>
                    <select id="sessionSelect" name="session" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Select a session --</option>
                        @foreach($sessions as $session)
                            <option value="{{ $session->id }}" @if($selectedSession && $selectedSession->id == $session->id) selected @endif>
                                {{ $session->session }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="termContainer" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Academic Term</label>
                    <select id="termSelect" name="term" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Select a term --</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Results Display -->
        <div id="resultsContainer">
            @if($selectedChild && $selectedSession && $selectedTerm)
                @include('parent-portal.results-content')
            @elseif($selectedChild)
                <div class="bg-white shadow-md rounded-lg p-12 text-center">
                    <p class="text-gray-500 text-lg">Please select both a session and term to view results</p>
                </div>
            @else
                <div class="bg-white shadow-md rounded-lg p-12 text-center">
                    <p class="text-gray-500 text-lg">Select a child to view their results</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

<script>
function initializeResultsPage() {
    const studentSelect = document.getElementById('studentSelect');
    const sessionSelect = document.getElementById('sessionSelect');
    const termSelect = document.getElementById('termSelect');
    const sessionContainer = document.getElementById('sessionContainer');
    const termContainer = document.getElementById('termContainer');
    const resultsContainer = document.getElementById('resultsContainer');

    // Make sure elements exist
    if (!studentSelect || !sessionSelect || !termSelect) {
        return;
    }

    // Remove old event listeners by cloning
    const newStudentSelect = studentSelect.cloneNode(true);
    const newSessionSelect = sessionSelect.cloneNode(true);
    const newTermSelect = termSelect.cloneNode(true);
    
    studentSelect.parentNode.replaceChild(newStudentSelect, studentSelect);
    sessionSelect.parentNode.replaceChild(newSessionSelect, sessionSelect);
    termSelect.parentNode.replaceChild(newTermSelect, termSelect);

    // Get fresh references after cloning
    const freshStudentSelect = document.getElementById('studentSelect');
    const freshSessionSelect = document.getElementById('sessionSelect');
    const freshTermSelect = document.getElementById('termSelect');

    // When student changes
    freshStudentSelect.addEventListener('change', async function() {
        const studentId = this.value;
        
        if (!studentId) {
            sessionContainer.style.display = 'none';
            termContainer.style.display = 'none';
            resultsContainer.innerHTML = '<div class="bg-white shadow-md rounded-lg p-12 text-center"><p class="text-gray-500 text-lg">Select a child to view their results</p></div>';
            return;
        }

        sessionContainer.style.display = 'block';
        freshSessionSelect.value = '';
        termContainer.style.display = 'none';
        freshTermSelect.innerHTML = '<option value="">-- Select a term --</option>';
        
        updateResults();
    });

    // When session changes
    freshSessionSelect.addEventListener('change', async function() {
        const sessionId = this.value;
        
        if (!sessionId) {
            termContainer.style.display = 'none';
            freshTermSelect.innerHTML = '<option value="">-- Select a term --</option>';
            updateResults();
            return;
        }

        // Fetch terms for this session
        try {
            const response = await fetch(`/parent-portal/results/terms/${sessionId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            // Populate terms dropdown
            freshTermSelect.innerHTML = '<option value="">-- Select a term --</option>';
            data.terms.forEach(term => {
                const option = document.createElement('option');
                option.value = term.id;
                option.textContent = term.name;
                freshTermSelect.appendChild(option);
            });
            
            termContainer.style.display = 'block';
            freshTermSelect.value = '';
        } catch (error) {
            console.error('Error fetching terms:', error);
        }
        
        updateResults();
    });

    // When term changes
    freshTermSelect.addEventListener('change', function() {
        updateResults();
    });

    // Update results
    async function updateResults() {
        const studentId = freshStudentSelect.value;
        const sessionId = freshSessionSelect.value;
        const termId = freshTermSelect.value;

        if (!studentId || !sessionId || !termId) {
            if (!studentId) {
                resultsContainer.innerHTML = '<div class="bg-white shadow-md rounded-lg p-12 text-center"><p class="text-gray-500 text-lg">Select a child to view their results</p></div>';
            } else if (!sessionId) {
                resultsContainer.innerHTML = '<div class="bg-white shadow-md rounded-lg p-12 text-center"><p class="text-gray-500 text-lg">Please select a session</p></div>';
            } else {
                resultsContainer.innerHTML = '<div class="bg-white shadow-md rounded-lg p-12 text-center"><p class="text-gray-500 text-lg">Please select both a session and term to view results</p></div>';
            }
            return;
        }

        try {
            const response = await fetch(`/parent-portal/results?student=${studentId}&session=${sessionId}&term=${termId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            resultsContainer.innerHTML = data.html;
        } catch (error) {
            console.error('Error fetching results:', error);
            resultsContainer.innerHTML = '<div class="bg-white shadow-md rounded-lg p-12 text-center"><p class="text-gray-500 text-lg">Error loading results</p></div>';
        }
    }
}

// Initialize on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeResultsPage);
} else {
    initializeResultsPage();
}

// Reinitialize on SPA navigation
document.addEventListener('spaContentLoaded', initializeResultsPage);
document.addEventListener('pageLoaded', initializeResultsPage);

// Fallback: Try to reinitialize after a short delay when page is visible
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible') {
        setTimeout(initializeResultsPage, 100);
    }
});
</script>

