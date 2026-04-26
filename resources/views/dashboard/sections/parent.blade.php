<!-- Parent Dashboard Stats -->
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
        <i class="fas fa-wallet text-teal-600"></i>Billing & Payment Status
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($data['stats'] as $index => $stat)
            <div class="stat-card from-{{ $stat['color'] }}-500 to-{{ $stat['color'] }}-600" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-{{ $stat['color'] }}-100 text-sm font-medium">{{ $stat['label'] }}</p>
                    <h3 class="text-3xl font-bold mt-2">
                        @if(isset($stat['format']) && $stat['format'] === 'currency')
                            ₦{{ number_format($stat['value'], 0) }}
                        @else
                            {{ $stat['value'] }}
                        @endif
                    </h3>
                    <p class="text-{{ $stat['color'] }}-100 text-xs mt-2">
                        <i class="fas fa-info-circle"></i> {{ $stat['trend'] }}
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-4">
                    <i class="{{ $stat['icon'] }} text-3xl"></i>
                </div>
            </div>
        </div>
    @endforeach
    </div>
</div>

<!-- Parent Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <a href="{{ route('parent-portal.bills') }}" class="group p-4 bg-red-50 border-2 border-red-200 rounded-lg hover:border-red-600 hover:bg-red-100 transition-all" data-aos="zoom-in" data-aos-delay="100">
        <i class="fas fa-file-invoice-dollar text-2xl text-red-600 mb-2 block"></i>
        <p class="font-semibold text-gray-900 text-sm">View Bills</p>
        <p class="text-xs text-gray-600">Check school charges</p>
    </a>
    <a href="{{ route('parent-portal.payment-history') }}" class="group p-4 bg-green-50 border-2 border-green-200 rounded-lg hover:border-green-600 hover:bg-green-100 transition-all" data-aos="zoom-in" data-aos-delay="200">
        <i class="fas fa-history text-2xl text-green-600 mb-2 block"></i>
        <p class="font-semibold text-gray-900 text-sm">Payment History</p>
        <p class="text-xs text-gray-600">Track all transactions</p>
    </a>
    <a href="{{ route('parent-portal.children') }}" class="group p-4 bg-blue-50 border-2 border-blue-200 rounded-lg hover:border-blue-600 hover:bg-blue-100 transition-all" data-aos="zoom-in" data-aos-delay="300">
        <i class="fas fa-children text-2xl text-blue-600 mb-2 block"></i>
        <p class="font-semibold text-gray-900 text-sm">My Children</p>
        <p class="text-xs text-gray-600">View children profiles</p>
    </a>
    <a href="{{ route('parent-portal.dashboard') }}" class="group p-4 bg-purple-50 border-2 border-purple-200 rounded-lg hover:border-purple-600 hover:bg-purple-100 transition-all" data-aos="zoom-in" data-aos-delay="400">
        <i class="fas fa-book text-2xl text-purple-600 mb-2 block"></i>
        <p class="font-semibold text-gray-900 text-sm">Children Results</p>
        <p class="text-xs text-gray-600">Check academic progress</p>
    </a>
</div>

<!-- Children Overview -->
@if($data['childrenOverview'])
<div class="card" data-aos="fade-up" data-aos-delay="200">
    <div class="card-header">
        <h2 class="text-lg font-semibold text-gray-900">Your Children</h2>
    </div>
    <div class="card-body">
        <div class="space-y-4">
            @forelse($data['childrenOverview'] as $child)
                <div class="p-4 border-2 border-gray-200 rounded-lg hover:border-primary-400 transition-colors">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">{{ $child['name'] }}</h3>
                            <div class="grid grid-cols-2 gap-2 mt-2 text-sm">
                                <div>
                                    <p class="text-gray-600">Class</p>
                                    <p class="font-medium text-gray-900">{{ $child['class'] }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Admission No.</p>
                                    <p class="font-medium text-gray-900">{{ $child['admission'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            @if($child['outstanding'] > 0)
                                <p class="text-sm text-red-600 font-semibold">Outstanding</p>
                                <p class="text-2xl font-bold text-red-600">₦{{ number_format($child['outstanding'], 0) }}</p>
                            @else
                                <p class="text-sm text-green-600 font-semibold">No Bills</p>
                                <p class="text-2xl font-bold text-green-600">✓</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex gap-2 mt-3">
                        <a href="#" class="flex-1 btn btn-sm btn-primary text-center">
                            <i class="fas fa-eye mr-1"></i> View Details
                        </a>
                        @if($child['outstanding'] > 0)
                            <a href="#" class="flex-1 btn btn-sm btn-outline-primary text-center">
                                <i class="fas fa-credit-card mr-1"></i> Pay Bill
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-3"></i>
                    <p>No children registered</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endif

<!-- Payment Reminders -->
@if($data['stats'][1]['value'] > 0)
<div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded-r-lg" data-aos="flip-up" data-aos-delay="300">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <i class="fas fa-bell text-orange-600 text-2xl"></i>
        </div>
        <div class="ml-3">
            <h3 class="font-semibold text-orange-900">Payment Reminder</h3>
            <p class="text-orange-700 mt-1 text-sm">You have outstanding bills totaling <strong>₦{{ number_format($data['stats'][1]['value'], 0) }}</strong>. Please settle to avoid any inconvenience.</p>
            <a href="{{ route('parent-portal.bills') }}" class="text-orange-700 hover:text-orange-900 font-medium text-sm mt-2 inline-flex items-center">
                <i class="fas fa-arrow-right mr-1"></i> Go to Bills
            </a>
        </div>
    </div>
</div>
@endif

<!-- Quick Links -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4" data-aos="fade-up" data-aos-delay="400">
    <a href="{{ route('parent-portal.children') }}" class="p-4 bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-200 rounded-lg hover:border-blue-400 transition-all text-center">
        <i class="fas fa-user-graduate text-3xl text-blue-600 mb-2"></i>
        <p class="font-semibold text-gray-900">My Children</p>
    </a>
    <a href="{{ route('parent-portal.results') }}" class="p-4 bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-200 rounded-lg hover:border-green-400 transition-all text-center">
        <i class="fas fa-chart-line text-3xl text-green-600 mb-2"></i>
        <p class="font-semibold text-gray-900">Results</p>
    </a>
    <a href="{{ route('parent-portal.bills') }}" class="p-4 bg-gradient-to-br from-purple-50 to-purple-100 border-2 border-purple-200 rounded-lg hover:border-purple-400 transition-all text-center">
        <i class="fas fa-money-bill-wave text-3xl text-purple-600 mb-2"></i>
        <p class="font-semibold text-gray-900">Bills</p>
    </a>
    <a href="{{ route('parent-portal.payment-history') }}" class="p-4 bg-gradient-to-br from-orange-50 to-orange-100 border-2 border-orange-200 rounded-lg hover:border-orange-400 transition-all text-center">
        <i class="fas fa-history text-3xl text-orange-600 mb-2"></i>
        <p class="font-semibold text-gray-900">History</p>
    </a>
</div>
