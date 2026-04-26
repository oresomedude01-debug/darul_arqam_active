@extends('layouts.spa')

@section('title', 'Registration Tokens')

@section('breadcrumb')
    <span class="text-gray-400">Student Management</span>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Registration Tokens</span>
@endsection

@section('content')
<div class="space-y-6" x-data="tokenManagement()">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Registration Tokens</h1>
            <p class="text-gray-600 mt-1">Generate and manage enrollment tokens for new student registrations</p>
        </div>
        <div class="flex items-center space-x-3">
            <button @click="showBulkActions = !showBulkActions"
                    x-show="selectedTokens.length > 0"
                    class="btn btn-outline">
                <i class="fas fa-tasks mr-2"></i>
                Bulk Actions (<span x-text="selectedTokens.length"></span>)
            </button>
            <a href="{{ route('tokens.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>
                Generate Tokens
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success fade-in">
        <i class="fas fa-check-circle text-xl"></i>
        <p>{{ session('success') }}</p>
    </div>
    @endif

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Tokens</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-ticket-alt text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Active</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['active'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Consumed</p>
                        <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['consumed'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-check text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Expired</p>
                        <p class="text-2xl font-bold text-orange-600 mt-1">{{ $stats['expired'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-orange-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Disabled</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['disabled'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-ban text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions Panel -->
    <div x-show="showBulkActions" x-collapse class="card">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-600">
                    <span x-text="selectedTokens.length"></span> token(s) selected
                </p>
                <div class="flex space-x-3">
                    <form method="POST" action="{{ route('tokens.bulk-disable') }}" @submit="return confirm('Disable selected tokens?')">
                        @csrf
                        <input type="hidden" name="token_ids" :value="JSON.stringify(selectedTokens)">
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fas fa-ban mr-2"></i>
                            Disable Selected
                        </button>
                    </form>
                    <form method="POST" action="{{ route('tokens.bulk-enable') }}" @submit="return confirm('Enable selected tokens?')">
                        @csrf
                        <input type="hidden" name="token_ids" :value="JSON.stringify(selectedTokens)">
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="fas fa-check mr-2"></i>
                            Enable Selected
                        </button>
                    </form>
                    <button @click="clearSelection()" class="btn btn-sm btn-outline">
                        Clear Selection
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('tokens.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search token code..."
                           class="form-input">
                </div>

                <!-- Status Filter -->
                <div>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="consumed" {{ request('status') === 'consumed' ? 'selected' : '' }}>Consumed</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="disabled" {{ request('status') === 'disabled' ? 'selected' : '' }}>Disabled</option>
                    </select>
                </div>

                <!-- Session Filter -->
                <div>
                    <select name="session" class="form-select">
                        <option value="">All Sessions</option>
                        <option value="2025/2026" {{ request('session') === '2025/2026' ? 'selected' : '' }}>2025/2026</option>
                        <option value="2024/2025" {{ request('session') === '2024/2025' ? 'selected' : '' }}>2024/2025</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex space-x-2">
                    <button type="submit" class="btn btn-primary flex-1">
                        <i class="fas fa-filter mr-2"></i>
                        Filter
                    </button>
                    <a href="{{ route('tokens.index') }}" class="btn btn-outline">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tokens Table -->
    <div class="card">
        <div class="card-header">
            <h2 class="text-lg font-semibold text-gray-900">All Tokens</h2>
            <div class="flex items-center space-x-2">
                <label class="flex items-center space-x-2">
                    <input type="checkbox"
                           @change="toggleSelectAll($event)"
                           class="form-checkbox">
                    <span class="text-sm text-gray-600">Select All</span>
                </label>
            </div>
        </div>
        <div class="card-body p-0">
            <!-- Desktop Table -->
            <div class="overflow-x-auto hidden md:block">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-12">
                                <input type="checkbox" class="form-checkbox" @change="toggleSelectAll($event)">
                            </th>
                            <th>Token Code</th>
                            <th>Status</th>
                            <th>Session/Year</th>
                            <th>Class Level</th>
                            <th>Created</th>
                            <th>Expires</th>
                            <th>Used By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tokens as $token)
                        <tr>
                            <td>
                                <input type="checkbox"
                                       class="form-checkbox"
                                       :checked="selectedTokens.includes({{ $token->id }})"
                                       @change="toggleToken({{ $token->id }})">
                            </td>
                            <td>
                                <div class="flex items-center space-x-2">
                                    <code class="px-2 py-1 bg-gray-100 rounded text-sm font-mono">
                                        {{ $token->code }}
                                    </code>
                                    <button @click="copyToClipboard('{{ $token->code }}')"
                                            class="text-gray-400 hover:text-gray-600"
                                            data-tooltip="Copy">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </td>
                            <td>
                                @if($token->status === 'active')
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle mr-1"></i> Active
                                    </span>
                                @elseif($token->status === 'consumed')
                                    <span class="badge badge-info">
                                        <i class="fas fa-user-check mr-1"></i> Consumed
                                    </span>
                                @elseif($token->status === 'expired')
                                    <span class="badge badge-warning">
                                        <i class="fas fa-clock mr-1"></i> Expired
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        <i class="fas fa-ban mr-1"></i> Disabled
                                    </span>
                                @endif
                            </td>
                            <td>{{ $token->session_year ?? '-' }}</td>
                            <td>
                                @if($token->schoolClass)
                                    <span class="badge badge-primary">{{ $token->schoolClass->name }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-sm">
                                    <div class="text-gray-900">{{ $token->created_at->format('M d, Y') }}</div>
                                    <div class="text-gray-500">{{ $token->created_at->format('h:i A') }}</div>
                                </div>
                            </td>
                            <td>
                                @if($token->expires_at)
                                    <div class="text-sm">
                                        <div class="text-gray-900">{{ $token->expires_at->format('M d, Y') }}</div>
                                        @if($token->expires_at->isPast())
                                            <div class="text-red-500 text-xs">Expired</div>
                                        @else
                                            <div class="text-gray-500 text-xs">{{ $token->expires_at->diffForHumans() }}</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">No expiry</span>
                                @endif
                            </td>
                            <td>
                                @if($token->user && $token->user->profile)
                                    <a href="{{ route('students.show', $token->user->profile->id) }}"
                                       class="text-primary-600 hover:text-primary-700">
                                        <div class="text-sm font-medium">{{ $token->user->profile->full_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $token->user->profile->admission_number ?? '-' }}</div>
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('tokens.show', $token->id) }}"
                                       class="text-blue-600 hover:text-blue-700"
                                       data-tooltip="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if($token->status === 'active')
                                        <form method="POST" action="{{ route('tokens.update', $token->id) }}" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="disabled">
                                            <button type="submit"
                                                    class="text-orange-600 hover:text-orange-700"
                                                    data-tooltip="Disable"
                                                    onclick="return confirm('Disable this token?')">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @elseif($token->status === 'disabled')
                                        <form method="POST" action="{{ route('tokens.update', $token->id) }}" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="active">
                                            <button type="submit"
                                                    class="text-green-600 hover:text-green-700"
                                                    data-tooltip="Enable"
                                                    onclick="return confirm('Enable this token?')">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-8">
                                <div class="text-gray-400">
                                    <i class="fas fa-ticket-alt text-4xl mb-3"></i>
                                    <p>No registration tokens found</p>
                                    <a href="{{ route('tokens.create') }}" class="text-primary-600 hover:text-primary-700 mt-2 inline-block">
                                        Generate your first token
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Grid -->
            <div class="md:hidden space-y-3 p-4">
                @forelse($tokens as $token)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                    <div class="p-4 space-y-3">
                        <!-- Header: Token Code and Status -->
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <div class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-2">Token Code</div>
                                <div class="flex items-center space-x-2">
                                    <code class="px-3 py-2 bg-gradient-to-br from-gray-50 to-gray-100 rounded-md text-sm font-mono border border-gray-200">
                                        {{ $token->code }}
                                    </code>
                                    <button @click="copyToClipboard('{{ $token->code }}')"
                                            class="p-2 rounded-md hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors"
                                            title="Copy to clipboard">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-2">Status</div>
                                @if($token->status === 'active')
                                    <span class="badge badge-success inline-flex items-center">
                                        <i class="fas fa-check-circle mr-1"></i> Active
                                    </span>
                                @elseif($token->status === 'consumed')
                                    <span class="badge badge-info inline-flex items-center">
                                        <i class="fas fa-user-check mr-1"></i> Consumed
                                    </span>
                                @elseif($token->status === 'expired')
                                    <span class="badge badge-warning inline-flex items-center">
                                        <i class="fas fa-clock mr-1"></i> Expired
                                    </span>
                                @else
                                    <span class="badge badge-danger inline-flex items-center">
                                        <i class="fas fa-ban mr-1"></i> Disabled
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="border-t border-gray-100"></div>

                        <!-- Row 2: Session and Class -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-2">Session/Year</div>
                                <div class="text-sm font-medium text-gray-900">{{ $token->session_year ?? '—' }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-2">Class Level</div>
                                @if($token->schoolClass)
                                    <span class="badge badge-primary">{{ $token->schoolClass->name }}</span>
                                @else
                                    <div class="text-sm text-gray-400">—</div>
                                @endif
                            </div>
                        </div>

                        <!-- Row 3: Created and Expires -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-2">Created</div>
                                <div class="text-sm font-medium text-gray-900">{{ $token->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $token->created_at->format('h:i A') }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-2">Expires</div>
                                @if($token->expires_at)
                                    <div class="text-sm font-medium text-gray-900">{{ $token->expires_at->format('M d, Y') }}</div>
                                    @if($token->expires_at->isPast())
                                        <div class="text-red-600 text-xs font-medium">Expired</div>
                                    @else
                                        <div class="text-gray-500 text-xs">{{ $token->expires_at->diffForHumans() }}</div>
                                    @endif
                                @else
                                    <div class="text-sm text-gray-400">No expiry</div>
                                @endif
                            </div>
                        </div>

                        <!-- Row 4: Used By -->
                        <div>
                            <div class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-2">Used By</div>
                            @if($token->user && $token->user->profile)
                                <a href="{{ route('students.show', $token->user->profile->id) }}"
                                   class="flex flex-col hover:bg-gray-50 p-2 rounded-md transition-colors">
                                    <div class="text-sm font-medium text-primary-600">{{ $token->user->profile->full_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $token->user->profile->admission_number ?? '—' }}</div>
                                </a>
                            @else
                                <div class="text-sm text-gray-400">—</div>
                            @endif
                        </div>

                        <div class="border-t border-gray-100"></div>

                        <!-- Row 5: Actions and Checkbox -->
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('tokens.show', $token->id) }}"
                                   class="inline-flex items-center justify-center p-2.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors"
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if($token->status === 'active')
                                    <form method="POST" action="{{ route('tokens.update', $token->id) }}" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="disabled">
                                        <button type="submit"
                                                class="inline-flex items-center justify-center p-2.5 rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-100 transition-colors"
                                                title="Disable token"
                                                onclick="return confirm('Disable this token?')">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
                                @elseif($token->status === 'disabled')
                                    <form method="POST" action="{{ route('tokens.update', $token->id) }}" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="active">
                                        <button type="submit"
                                                class="inline-flex items-center justify-center p-2.5 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 transition-colors"
                                                title="Enable token"
                                                onclick="return confirm('Enable this token?')">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <label class="flex items-center gap-2 cursor-pointer p-2 rounded-md hover:bg-gray-50">
                                <input type="checkbox"
                                       class="form-checkbox"
                                       :checked="selectedTokens.includes({{ $token->id }})"
                                       @change="toggleToken({{ $token->id }})">
                                <span class="text-sm text-gray-600">Select</span>
                            </label>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-12 bg-white rounded-lg border border-gray-200">
                    <div class="text-gray-400">
                        <i class="fas fa-ticket-alt text-5xl mb-4 opacity-50"></i>
                        <p class="text-lg font-medium text-gray-600">No registration tokens found</p>
                        <a href="{{ route('tokens.create') }}" class="btn btn-primary mt-4 inline-block">
                            <i class="fas fa-plus mr-2"></i>Generate your first token
                        </a>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        @if($tokens->hasPages())
        <div class="card-footer">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-semibold text-gray-900">{{ $tokens->firstItem() }}</span> to
                        <span class="font-semibold text-gray-900">{{ $tokens->lastItem() }}</span> of
                        <span class="font-semibold text-gray-900">{{ $tokens->total() }}</span> results
                    </p>
                </div>
                <div class="flex justify-center md:justify-end">
                    {{ $tokens->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function tokenManagement() {
    return {
        selectedTokens: [],
        showBulkActions: false,

        toggleToken(tokenId) {
            const index = this.selectedTokens.indexOf(tokenId);
            if (index > -1) {
                this.selectedTokens.splice(index, 1);
            } else {
                this.selectedTokens.push(tokenId);
            }
            this.showBulkActions = this.selectedTokens.length > 0;
        },

        toggleSelectAll(event) {
            if (event.target.checked) {
                // Select all visible tokens
                this.selectedTokens = @json($tokens->pluck('id'));
            } else {
                this.selectedTokens = [];
            }
            this.showBulkActions = this.selectedTokens.length > 0;
        },

        clearSelection() {
            this.selectedTokens = [];
            this.showBulkActions = false;
        },

        copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                Toast.success('Token code copied to clipboard!');
            });
        }
    }
}
</script>
@endpush
@endsection
