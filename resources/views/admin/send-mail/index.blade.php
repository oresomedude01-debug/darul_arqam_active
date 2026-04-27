@extends('layouts.spa')

@section('title', 'Send Mail')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Send Mail</h1>
        <p class="mt-2 text-gray-600">Send official emails directly to users from the system</p>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-4 rounded-lg flex items-center gap-2">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
    @endif

    @if(session('warning'))
    <div class="mb-6 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-4 rounded-lg flex items-center gap-2">
        <i class="fas fa-exclamation-triangle"></i>
        {{ session('warning') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-4 rounded-lg flex items-center gap-2">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-4 rounded-lg">
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Send Mail Form -->
    <form action="{{ route('admin.send-mail.send') }}" method="POST" x-data="sendMailForm()" class="space-y-6">
        @csrf

        <!-- Recipients Selection -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-users text-primary-500"></i>
                    Recipients
                </h2>
            </div>
            <div class="p-6">
                <!-- Quick Actions -->
                <div class="flex flex-wrap gap-2 mb-4">
                    <button type="button" @click="selectAll()" class="px-3 py-1.5 text-xs font-medium bg-primary-50 text-primary-700 hover:bg-primary-100 rounded-lg transition">
                        <i class="fas fa-check-double mr-1"></i>Select All
                    </button>
                    <button type="button" @click="deselectAll()" class="px-3 py-1.5 text-xs font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 rounded-lg transition">
                        <i class="fas fa-times mr-1"></i>Deselect All
                    </button>
                    <span class="text-sm text-gray-500 flex items-center ml-auto" x-text="selectedCount + ' selected'"></span>
                </div>

                <!-- Search -->
                <div class="relative mb-4">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" x-model="searchQuery" placeholder="Search users by name or email..."
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                </div>

                <!-- Users List -->
                <div class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg divide-y divide-gray-100">
                    @foreach($users as $user)
                    <label x-show="matchesSearch('{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}')"
                           class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer transition">
                        <input type="checkbox" name="recipients[]" value="{{ $user->id }}"
                               x-model="selectedRecipients"
                               class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Email Content -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-envelope text-primary-500"></i>
                    Email Content
                </h2>
            </div>
            <div class="p-6 space-y-5">
                <!-- Subject -->
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1.5">Subject <span class="text-red-500">*</span></label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required
                           placeholder="Enter email subject..."
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                </div>

                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-1.5">Priority</label>
                    <select id="priority" name="priority"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition bg-white">
                        <option value="normal">Normal</option>
                        <option value="high">High Priority</option>
                    </select>
                </div>

                <!-- Message Body -->
                <div>
                    <label for="message_body" class="block text-sm font-medium text-gray-700 mb-1.5">Message <span class="text-red-500">*</span></label>
                    <textarea id="message_body" name="message_body" rows="10" required
                              placeholder="Write your message here..."
                              class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition resize-y">{{ old('message_body') }}</textarea>
                    <p class="text-xs text-gray-400 mt-1.5"><i class="fas fa-info-circle mr-1"></i>The message will be formatted and sent as an official school email.</p>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('dashboard') }}" class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                Cancel
            </a>
            <button type="submit" :disabled="selectedCount === 0"
                    :class="selectedCount === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-primary-700 shadow-lg shadow-primary-500/30'"
                    class="px-8 py-3 text-sm font-medium text-white bg-primary-600 rounded-lg transition flex items-center gap-2">
                <i class="fas fa-paper-plane"></i>
                Send Email
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function sendMailForm() {
    return {
        searchQuery: '',
        selectedRecipients: [],
        
        get selectedCount() {
            return this.selectedRecipients.length;
        },
        
        matchesSearch(name, email) {
            if (!this.searchQuery) return true;
            const q = this.searchQuery.toLowerCase();
            return name.toLowerCase().includes(q) || email.toLowerCase().includes(q);
        },
        
        selectAll() {
            const checkboxes = document.querySelectorAll('input[name="recipients[]"]');
            this.selectedRecipients = [];
            checkboxes.forEach(cb => {
                const label = cb.closest('label');
                if (label && label.style.display !== 'none') {
                    this.selectedRecipients.push(cb.value);
                }
            });
        },
        
        deselectAll() {
            this.selectedRecipients = [];
        }
    };
}
</script>
@endpush
@endsection
