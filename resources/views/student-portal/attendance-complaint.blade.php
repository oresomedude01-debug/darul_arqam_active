@extends('student-portal.layout')

@section('portal-title', 'File Attendance Complaint')

@section('student-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-orange-600 to-red-600 rounded-2xl p-8 text-white shadow-lg">
        <h1 class="text-3xl font-bold flex items-center gap-3">
            <i class="fas fa-exclamation-circle"></i>File Attendance Complaint
        </h1>
        <p class="text-orange-100 mt-2">Contest or explain your absence</p>
    </div>

    <!-- Recent Absences -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-orange-600 to-red-600 px-6 py-5 relative overflow-hidden">
            <div class="absolute right-0 top-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20 blur-2xl"></div>
            <h2 class="text-xl font-bold text-white flex items-center gap-3 relative">
                <i class="fas fa-history text-orange-200"></i>Recent Absences
            </h2>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse($recentAbsences as $absence)
                <div class="p-6 hover:bg-gray-50 transition-colors group">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-bold rounded-full">
                                    {{ ucfirst($absence->status) }}
                                </span>
                                <p class="font-semibold text-gray-900">{{ $absence->academicTerm?->name ?? 'N/A' }}</p>
                            </div>
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-calendar text-gray-400 mr-2"></i>{{ $absence->date?->format('M d, Y') ?? 'N/A' }}
                            </p>
                        </div>
                        <button type="button" 
                                onclick="selectAbsence({{ $absence->id }}, '{{ $absence->date?->format('M d, Y') }}', '{{ $absence->academicTerm?->name }}')"
                                class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-semibold rounded-lg transition-colors group-hover:scale-105 transform duration-200">
                            Select & Complain
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <i class="fas fa-check-circle text-4xl text-green-300 mb-3 block"></i>
                    <p class="text-gray-600 font-medium">Great! No recent absences recorded.</p>
                    <p class="text-gray-500 text-sm mt-1">Keep up the good attendance!</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Complaint Form -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden" id="complaintForm" style="display: none;">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-5 relative overflow-hidden">
            <div class="absolute right-0 top-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20 blur-2xl"></div>
            <h2 class="text-xl font-bold text-white flex items-center gap-3 relative">
                <i class="fas fa-pen-to-square text-blue-200"></i>File Your Complaint
            </h2>
        </div>

        <form action="{{ route('student-portal.attendance.complaint.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Selected Absence Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-900">
                    <span class="font-semibold">Selected Absence:</span>
                    <span id="selectedAbsenceInfo" class="text-blue-700">None selected</span>
                </p>
            </div>

            <!-- Hidden Attendance ID -->
            <input type="hidden" name="attendance_id" id="attendanceId" value="">

            <!-- Complaint Reason -->
            <div>
                <label for="reason" class="block text-sm font-semibold text-gray-900 mb-2">
                    <i class="fas fa-comment text-indigo-600 mr-2"></i>Reason for Complaint
                </label>
                <textarea name="reason" id="reason" rows="6"
                          placeholder="Please explain why you believe this absence was unjustified. Provide any supporting details or circumstances..."
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none @error('reason') border-red-500 @enderror"
                          required></textarea>
                @error('reason')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Guidelines -->
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                <h3 class="font-semibold text-amber-900 mb-2 flex items-center gap-2">
                    <i class="fas fa-lightbulb text-amber-600"></i>Guidelines
                </h3>
                <ul class="text-sm text-amber-800 space-y-1 list-disc list-inside">
                    <li>Be clear and concise in your explanation</li>
                    <li>Provide relevant dates and times if applicable</li>
                    <li>Include any supporting documents or evidence if needed</li>
                    <li>Your complaint will be reviewed by the administration</li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="flex-1 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i>Submit Complaint
                </button>
                <button type="button" onclick="hideComplaintForm()" class="flex-1 px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-900 font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i>Cancel
                </button>
            </div>
        </form>
    </div>

    <!-- Information Box -->
    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl border border-indigo-200 p-6 shadow-md">
        <h3 class="font-bold text-indigo-900 mb-4 flex items-center gap-2">
            <i class="fas fa-info-circle text-indigo-600"></i>How It Works
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <p class="font-semibold text-indigo-900 mb-1">1. Select Absence</p>
                <p class="text-indigo-700">Choose from your recent absences above</p>
            </div>
            <div>
                <p class="font-semibold text-indigo-900 mb-1">2. Explain</p>
                <p class="text-indigo-700">Provide a detailed reason for your complaint</p>
            </div>
            <div>
                <p class="font-semibold text-indigo-900 mb-1">3. Review</p>
                <p class="text-indigo-700">Administration will review and respond</p>
            </div>
        </div>
    </div>
</div>

<script>
function selectAbsence(attendanceId, date, term) {
    document.getElementById('attendanceId').value = attendanceId;
    document.getElementById('selectedAbsenceInfo').textContent = `${term} - ${date}`;
    document.getElementById('complaintForm').style.display = 'block';
    document.getElementById('reason').focus();
    document.getElementById('complaintForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function hideComplaintForm() {
    document.getElementById('complaintForm').style.display = 'none';
    document.getElementById('attendanceId').value = '';
    document.getElementById('reason').value = '';
    document.getElementById('selectedAbsenceInfo').textContent = 'None selected';
}
</script>

@endsection
