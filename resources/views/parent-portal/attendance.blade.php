@extends('layouts.spa')

@section('title', 'Children Attendance')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Children's Attendance</h1>
            <p class="mt-2 text-sm text-gray-600">Track your children's attendance records</p>
        </div>

        <!-- Child Selection -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <form method="GET" class="flex gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Child</label>
                    <select name="student" onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Select a child --</option>
                        @foreach($children as $child)
                            <option value="{{ $child->id }}" @if($selectedChild && $selectedChild->id == $child->id) selected @endif>
                                {{ $child->first_name }} {{ $child->last_name }} ({{ $child->schoolClass->name ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        <!-- Attendance Records -->
        @if($selectedChild)
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700">
                    <h2 class="text-lg font-semibold text-white">
                        Attendance Records - {{ $selectedChild->first_name }} {{ $selectedChild->last_name }}
                    </h2>
                </div>

                @if($attendanceRecords->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Session</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Term</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($attendanceRecords as $record)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ \Carbon\Carbon::parse($record->attendance_date)->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            @if($record->status === 'present')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                    ✓ Present
                                                </span>
                                            @elseif($record->status === 'absent')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                                    ✗ Absent
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                                    ~ Late
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $record->academicSession->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $record->academicTerm->name ?? 'N/A' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t">
                        {{ $attendanceRecords->links() }}
                    </div>
                @else
                    <div class="px-6 py-12 text-center">
                        <p class="text-gray-500">No attendance records found</p>
                    </div>
                @endif
            </div>
        @else
            <div class="bg-white shadow-md rounded-lg p-12 text-center">
                <p class="text-gray-500 text-lg">Select a child to view attendance records</p>
            </div>
        @endif
    </div>
</div>
@endsection
