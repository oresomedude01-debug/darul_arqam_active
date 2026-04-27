@extends('layouts.spa')

@section('title', 'My Profile')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Profile Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-2xl font-bold">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ Auth::user()->name }}</h1>
                <p class="text-gray-600">{{ Auth::user()->email }}</p>
                <div class="mt-2">
                    <span class="inline-block px-3 py-1 bg-primary-100 text-primary-700 text-sm rounded-full font-medium">
                        {{ implode(', ', Auth::user()->roles->pluck('name')->toArray()) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Information -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Profile Information</h2>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <p class="text-gray-900">{{ Auth::user()->name }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <p class="text-gray-900">{{ Auth::user()->email }}</p>
            </div>
            
            @if($profile && $profile->phone)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <p class="text-gray-900">{{ $profile->phone }}</p>
            </div>
            @endif
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Member Since</label>
                <p class="text-gray-900">{{ Auth::user()->created_at ? Auth::user()->created_at->format('F j, Y') : 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-3">
        <a href="{{ route('profile.edit') }}"
           @click.prevent="navigate('{{ route('profile.edit') }}')"
           class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition text-center font-medium">
            Edit Profile
        </a>
        <a href="{{ route('dashboard') }}"
           @click.prevent="navigate('{{ route('dashboard') }}')"
           class="flex-1 px-4 py-2 bg-gray-200 text-gray-900 rounded-lg hover:bg-gray-300 transition text-center font-medium">
            Back to Dashboard
        </a>
    </div>
</div>
@endsection
