<!-- Admin Dashboard Stats -->
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
        <i class="fas fa-chart-pie text-primary-600"></i>School Overview
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

<!-- Admin Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <a href="{{ route('students.index') }}" class="group p-4 bg-blue-50 border-2 border-blue-200 rounded-lg hover:border-blue-600 hover:bg-blue-100 transition-all" data-aos="zoom-in" data-aos-delay="100">
        <i class="fas fa-users text-2xl text-blue-600 mb-2 block"></i>
        <p class="font-semibold text-gray-900 text-sm">Manage Students</p>
        <p class="text-xs text-gray-600">View & edit student records</p>
    </a>
    <a href="{{ route('teachers.index') }}" class="group p-4 bg-green-50 border-2 border-green-200 rounded-lg hover:border-green-600 hover:bg-green-100 transition-all" data-aos="zoom-in" data-aos-delay="200">
        <i class="fas fa-chalkboard-user text-2xl text-green-600 mb-2 block"></i>
        <p class="font-semibold text-gray-900 text-sm">Manage Teachers</p>
        <p class="text-xs text-gray-600">Assign roles & permissions</p>
    </a>
    <a href="{{ route('results.index') }}" class="group p-4 bg-purple-50 border-2 border-purple-200 rounded-lg hover:border-purple-600 hover:bg-purple-100 transition-all" data-aos="zoom-in" data-aos-delay="300">
        <i class="fas fa-file-contract text-2xl text-purple-600 mb-2 block"></i>
        <p class="font-semibold text-gray-900 text-sm">View Results</p>
        <p class="text-xs text-gray-600">Monitor academic performance</p>
    </a>
    <a href="{{ route('billing.generate-bills') }}" class="group p-4 bg-orange-50 border-2 border-orange-200 rounded-lg hover:border-orange-600 hover:bg-orange-100 transition-all" data-aos="zoom-in" data-aos-delay="400">
        <i class="fas fa-money-bill-wave text-2xl text-orange-600 mb-2 block"></i>
        <p class="font-semibold text-gray-900 text-sm">Billing</p>
        <p class="text-xs text-gray-600">Manage fees & payments</p>
    </a>
</div>

<!-- Results Overview -->
@if($data['resultsOverview'])
<div class="card" data-aos="fade-up" data-aos-delay="200">
    <div class="card-header">
        <h2 class="text-lg font-semibold text-gray-900">Results Overview by Term</h2>
    </div>
    <div class="card-body">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @forelse($data['resultsOverview'] as $overview)
                <div class="p-4 border-2 border-gray-200 rounded-lg text-center hover:border-primary-400 transition-colors">
                    <p class="text-sm text-gray-600 font-medium">{{ $overview['term'] }}</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ $overview['count'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Results Entered</p>
                </div>
            @empty
                <div class="col-span-3 text-center py-4 text-gray-500">No results data available</div>
            @endforelse
        </div>
    </div>
</div>
@endif

<!-- Recent Payments -->
@if(count($data['recentPayments']) > 0)
<div class="card" data-aos="fade-up" data-aos-delay="300">
    <div class="card-header">
        <h2 class="text-lg font-semibold text-gray-900">Recent Payments</h2>
        <span class="text-sm text-gray-600">Last 5 transactions</span>
    </div>
    <div class="card-body">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b-2 border-gray-200">
                    <tr>
                        <th class="text-left py-3 px-4">Student</th>
                        <th class="text-right py-3 px-4">Amount</th>
                        <th class="text-left py-3 px-4">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['recentPayments'] as $payment)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 px-4">{{ $payment['student'] }}</td>
                            <td class="py-3 px-4 text-right font-semibold text-green-600">₦{{ number_format($payment['amount'], 2) }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ $payment['date'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-gray-500">No recent payments</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Pending Bills Alert -->
@if($data['pendingBills'] > 0)
<div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg" data-aos="flip-up" data-aos-delay="400">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-lg font-semibold text-red-900">Pending Bills Alert</h3>
            <p class="text-red-700 mt-1">There are <strong>{{ $data['pendingBills'] }}</strong> outstanding bills that need attention.</p>
        </div>
    </div>
</div>
@endif
