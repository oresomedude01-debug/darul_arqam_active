@extends('student-portal.layout')

@section('portal-title', 'Attendance Record')

@section('student-content')
<div class="space-y-6">
    <!-- Attendance Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl border border-blue-200 p-6 hover:shadow-lg transition-all">
            <h3 class="text-sm font-semibold text-blue-900 mb-2 uppercase tracking-wide">Total Classes</h3>
            <p class="text-4xl font-bold text-blue-600">{{ $sessionStats['present'] ?? 0 }}</p>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl border border-green-200 p-6 hover:shadow-lg transition-all">
            <h3 class="text-sm font-semibold text-green-900 mb-2 uppercase tracking-wide">Present</h3>
            <p class="text-4xl font-bold text-green-600">{{ $sessionStats['present'] ?? 0 }}</p>
        </div>
        <div class="bg-gradient-to-br from-red-50 to-rose-50 rounded-2xl border border-red-200 p-6 hover:shadow-lg transition-all">
            <h3 class="text-sm font-semibold text-red-900 mb-2 uppercase tracking-wide">Absent</h3>
            <p class="text-4xl font-bold text-red-600">{{ $sessionStats['absent'] ?? 0 }}</p>
        </div>
        <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl border border-amber-200 p-6 hover:shadow-lg transition-all">
            <h3 class="text-sm font-semibold text-amber-900 mb-2 uppercase tracking-wide">Leave</h3>
            <p class="text-4xl font-bold text-amber-600">{{ $sessionStats['leave'] ?? 0 }}</p>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-all">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5 relative overflow-hidden">
            <div class="absolute right-0 top-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20 blur-2xl"></div>
            <h2 class="text-xl font-bold text-white flex items-center gap-3 relative">
                <i class="fas fa-calendar-alt text-purple-200"></i>Attendance History
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-indigo-50 to-purple-50 border-b-2 border-indigo-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-bold text-indigo-900">Date</th>
                        <th class="px-6 py-4 text-left text-sm font-bold text-indigo-900">Day</th>
                        <th class="px-6 py-4 text-left text-sm font-bold text-indigo-900">Session</th>
                        <th class="px-6 py-4 text-center text-sm font-bold text-indigo-900">Status</th>
                        <th class="px-6 py-4 text-left text-sm font-bold text-indigo-900">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($attendance as $record)
                        <tr class="hover:bg-indigo-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-900">{{ $record->date->format('M d, Y') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">{{ $record->date->format('l') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">{{ $record->academicSession->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($record->status === 'present')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 text-sm rounded-lg font-semibold border border-green-200">
                                        <i class="fas fa-check-circle text-lg"></i>Present
                                    </span>
                                @elseif($record->status === 'absent')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 text-red-700 text-sm rounded-lg font-semibold border border-red-200">
                                        <i class="fas fa-times-circle text-lg"></i>Absent
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-700 text-sm rounded-lg font-semibold border border-amber-200">
                                        <i class="fas fa-calendar-times text-lg"></i>Leave
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-600">{{ $record->remarks ?? '-' }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                        <i class="fas fa-calendar text-3xl text-gray-400"></i>
                                    </div>
                                    <p class="text-gray-600 font-medium">No attendance records</p>
                                    <p class="text-gray-400 text-sm mt-1">Your attendance records will appear here</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Attendance Guidelines -->
    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 rounded-2xl border-2 border-indigo-200 p-6">
        <h3 class="font-bold text-indigo-900 mb-4 flex items-center gap-2">
            <i class="fas fa-info-circle text-indigo-600"></i>Attendance Guidelines
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div class="flex gap-3">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-600 flex-shrink-0 font-bold">✓</span>
                <div>
                    <p class="font-semibold text-indigo-900">Present</p>
                    <p class="text-indigo-700 text-xs">You attended class on this day</p>
                </div>
            </div>
            <div class="flex gap-3">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-600 flex-shrink-0 font-bold">✕</span>
                <div>
                    <p class="font-semibold text-indigo-900">Absent</p>
                    <p class="text-indigo-700 text-xs">You missed class on this day</p>
                </div>
            </div>
            <div class="flex gap-3">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex-shrink-0 font-bold">-</span>
                <div>
                    <p class="font-semibold text-indigo-900">Leave</p>
                    <p class="text-indigo-700 text-xs">Approved leave or holiday</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
