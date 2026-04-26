@extends('layouts.spa')

@section('title', 'Edit Parent - ' . $parent->name)

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Edit Parent</h1>
        <p class="mt-2 text-gray-600">{{ $parent->name }}</p>
    </div>

    <!-- Form -->
    <div class="max-w-2xl">
        <form action="{{ route('admin.parents.update', $parent) }}" method="POST" class="bg-white rounded-lg shadow-md p-8">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">Full Name <span class="text-red-500">*</span></label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name', $parent->name) }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('name') border-red-500 @enderror"
                       placeholder="Enter parent's full name"
                       required>
                @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-6">
                <label for="email" class="block text-sm font-semibold text-gray-900 mb-2">Email Address <span class="text-red-500">*</span></label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email', $parent->email) }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('email') border-red-500 @enderror"
                       placeholder="Enter email address"
                       required>
                @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone -->
            <div class="mb-6">
                <label for="phone" class="block text-sm font-semibold text-gray-900 mb-2">Phone Number</label>
                <input type="text" 
                       id="phone" 
                       name="phone" 
                       value="{{ old('phone', $parent->profile?->phone) }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('phone') border-red-500 @enderror"
                       placeholder="Enter phone number">
                @error('phone')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Occupation -->
            <div class="mb-6">
                <label for="occupation" class="block text-sm font-semibold text-gray-900 mb-2">Occupation</label>
                <input type="text" 
                       id="occupation" 
                       name="occupation" 
                       value="{{ old('occupation', $parent->profile?->occupation) }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('occupation') border-red-500 @enderror"
                       placeholder="Enter occupation">
                @error('occupation')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Address -->
            <div class="mb-6">
                <label for="address" class="block text-sm font-semibold text-gray-900 mb-2">Address</label>
                <textarea id="address" 
                          name="address" 
                          rows="3" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('address') border-red-500 @enderror"
                          placeholder="Enter address">{{ old('address', $parent->profile?->address) }}</textarea>
                @error('address')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password (Optional) -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-blue-700 mb-3">Leave password blank to keep current password</p>
                
                <div class="mb-4">
                    <label for="password" class="block text-sm font-semibold text-gray-900 mb-2">New Password</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('password') border-red-500 @enderror"
                           placeholder="Leave blank to keep current password">
                    @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-900 mb-2">Confirm New Password</label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('password_confirmation') border-red-500 @enderror"
                           placeholder="Confirm new password">
                    @error('password_confirmation')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-6 rounded-lg transition">
                    <i class="fas fa-save mr-2"></i>Update Parent
                </button>
                <a href="{{ route('admin.parents.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-900 font-medium py-2 px-6 rounded-lg transition">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
