@extends('layouts.spa')

@section('content')
<div class="min-h-screen bg-gray-50 px-6 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Breadcrumb & Header -->
        <div class="mb-8">
            <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
                <a href="{{ route('results.index') }}" class="text-primary-600 hover:text-primary-700">Results</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span>Class Report Card</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Class Report Card</h1>
            <p class="text-gray-600 mt-2">Select session, term, and class to generate a comprehensive class report</p>
        </div>

        <!-- Selection Form -->
        <div class="bg-white rounded-lg shadow p-8">
            <form id="selectionForm" method="GET" action="">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Academic Session -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">
                            <i class="fas fa-calendar mr-2 text-primary-600"></i>Academic Session / Year
                        </label>
                        <select id="sessionSelect" name="session_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                            <option value="">-- Select Academic Session --</option>
                            @foreach($sessions as $session)
                                <option value="{{ $session->id }}">{{ $session->session }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Select the academic session</p>
                    </div>

                    <!-- Term -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">
                            <i class="fas fa-bookmark mr-2 text-primary-600"></i>Term
                        </label>
                        <select id="termSelect" name="term_id" required disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 disabled:bg-gray-50 disabled:text-gray-500">
                            <option value="">-- Select Term --</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Select session first</p>
                    </div>

                    <!-- Class -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">
                            <i class="fas fa-chalkboard-user mr-2 text-primary-600"></i>Class
                        </label>
                        <select id="classSelect" name="class_id" required disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 disabled:bg-gray-50 disabled:text-gray-500">
                            <option value="">-- Select Class --</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Select term first</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 flex gap-4">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors gap-2">
                        <i class="fas fa-arrow-right"></i>View Report Card
                    </button>
                    <a href="{{ route('results.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-300 text-gray-900 font-medium rounded-lg hover:bg-gray-400 transition-colors gap-2">
                        <i class="fas fa-arrow-left"></i>Back
                    </a>
                </div>
            </form>

            <!-- Information Box -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <p class="text-sm text-blue-900">
                    <i class="fas fa-info-circle mr-2 font-bold"></i>
                    <strong>Class Report Card</strong> provides a comprehensive overview of all students' performance in a specific class for a selected term, including statistics, individual student scores, and comparative analysis.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    const sessionData = {!! json_encode($sessionData) !!};
    const termData = {!! json_encode($termData) !!};
    const classData = {!! json_encode($classData) !!};

    // Handle Session Selection
    document.getElementById('sessionSelect').addEventListener('change', function() {
        const sessionId = this.value;
        const termSelect = document.getElementById('termSelect');
        
        termSelect.innerHTML = '<option value="">-- Select Term --</option>';
        termSelect.disabled = !sessionId;
        
        if (sessionId && termData[sessionId]) {
            termData[sessionId].forEach(term => {
                const option = document.createElement('option');
                option.value = term.id;
                option.textContent = term.term;
                termSelect.appendChild(option);
            });
        }
        
        // Reset class select
        document.getElementById('classSelect').innerHTML = '<option value="">-- Select Class --</option>';
        document.getElementById('classSelect').disabled = true;
    });

    // Handle Term Selection
    document.getElementById('termSelect').addEventListener('change', function() {
        const termId = this.value;
        const classSelect = document.getElementById('classSelect');
        
        classSelect.innerHTML = '<option value="">-- Select Class --</option>';
        classSelect.disabled = !termId;
        
        if (termId && classData[termId]) {
            classData[termId].forEach(cls => {
                const option = document.createElement('option');
                option.value = cls.id;
                option.textContent = cls.name;
                classSelect.appendChild(option);
            });
        }
    });

    // Form submission
    document.getElementById('selectionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const sessionSelect = document.getElementById('sessionSelect');
        const termSelect = document.getElementById('termSelect');
        const classSelect = document.getElementById('classSelect');

        if (!sessionSelect.value || !termSelect.value || !classSelect.value) {
            alert('Please select all required fields (Session, Term, and Class)');
            return;
        }

        // Navigate to the report page with parameters
        const sessionId = sessionSelect.value;
        const termId = termSelect.value;
        const classId = classSelect.value;
        const url = `/results/session/${sessionId}/term/${termId}/class/${classId}/report`;
        
        // Use the global navigate function from SPA
        if (window.navigate) {
            window.navigate(url);
        } else {
            window.location.href = url;
        }
    });
</script>
@endsection
