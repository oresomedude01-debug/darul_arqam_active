@extends('layouts.spa')

@section('title', 'Edit Profile')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Profile</h1>
        <p class="text-gray-600 mt-1">Update your account information</p>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('profile.update') }}" method="POST" data-ajax>
            @csrf
            
            <div class="space-y-4">
                <!-- Name - Only Admin can change -->
                @if(Auth::user()->hasRole('Administrator'))
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name', Auth::user()->name) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                @else
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text"
                           value="{{ Auth::user()->name }}"
                           disabled
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed">
                    <p class="text-gray-500 text-xs mt-1">Only administrators can change their name</p>
                </div>
                @endif

                <!-- Email - Students cannot change -->
                @if(!Auth::user()->hasRole('student'))
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email', Auth::user()->email) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                @else
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email"
                           value="{{ Auth::user()->email }}"
                           disabled
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed">
                    <p class="text-gray-500 text-xs mt-1">Students cannot change their email address</p>
                </div>
                @endif

                <!-- Phone - Everyone can change -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="tel"
                           id="phone"
                           name="phone"
                           value="{{ old('phone', $profile?->phone) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition @error('phone') border-red-500 @enderror">
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 mt-6">
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition font-medium">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
                <a href="{{ route('profile.show') }}"
                   @click.prevent="navigate('{{ route('profile.show') }}')"
                   class="flex-1 px-4 py-2 bg-gray-200 text-gray-900 rounded-lg hover:bg-gray-300 transition text-center font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
