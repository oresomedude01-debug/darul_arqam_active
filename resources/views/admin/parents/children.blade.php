@extends('layouts.spa')

@section('title', 'Children of ' . $parent->name)

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('admin.parents.index') }}" class="text-purple-600 hover:text-purple-800">
                <i class="fas fa-arrow-left"></i> Back to Parents
            </a>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">Children of {{ $parent->name }}</h1>
        <p class="mt-2 text-gray-600">Email: {{ $parent->email }}</p>
    </div>

    <!-- Children Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Child Name</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Email</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Role</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Class</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($parent->children as $child)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ $child->name }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-gray-600 text-sm">{{ $child->email }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            @forelse($child->roles as $role)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $role->name }}
                            </span>
                            @empty
                            <span class="text-gray-500 text-sm">No role</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-gray-600 text-sm">
                            {{ $child->profile?->class?->name ?? '-' }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('students.edit', $child) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            <i class="fas fa-eye mr-2"></i>View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 block opacity-50"></i>
                        No children linked to this parent
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
