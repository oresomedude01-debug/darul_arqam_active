<!-- Student Dashboard Stats -->
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
        <i class="fas fa-graduation-cap text-amber-600"></i>Academic Performance
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($data['stats'] as $stat)
            <div class="stat-card from-{{ $stat['color'] }}-500 to-{{ $stat['color'] }}-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-{{ $stat['color'] }}-100 text-sm font-medium">{{ $stat['label'] }}</p>
                    <h3 class="text-3xl font-bold mt-2">{{ $stat['value'] }}</h3>
                    <p class="text-{{ $stat['color'] }}-100 text-xs mt-2">
                        <i class="fas fa-star"></i> {{ $stat['trend'] }}
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

<!-- Student Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <a href="{{ route('results.index') }}" class="group p-4 bg-blue-50 border-2 border-blue-200 rounded-lg hover:border-blue-600 hover:bg-blue-100 transition-all">
        <i class="fas fa-chart-line text-2xl text-blue-600 mb-2 block"></i>
        <p class="font-semibold text-gray-900 text-sm">My Results</p>
        <p class="text-xs text-gray-600">View all test scores</p>
    </a>
    <a href="{{ route('dashboard') }}" class="group p-4 bg-green-50 border-2 border-green-200 rounded-lg hover:border-green-600 hover:bg-green-100 transition-all">
        <i class="fas fa-calendar-check text-2xl text-green-600 mb-2 block"></i>
        <p class="font-semibold text-gray-900 text-sm">Attendance</p>
        <p class="text-xs text-gray-600">Check attendance record</p>
    </a>
    <a href="{{ route('dashboard') }}" class="group p-4 bg-purple-50 border-2 border-purple-200 rounded-lg hover:border-purple-600 hover:bg-purple-100 transition-all">
        <i class="fas fa-user-circle text-2xl text-purple-600 mb-2 block"></i>
        <p class="font-semibold text-gray-900 text-sm">My Profile</p>
        <p class="text-xs text-gray-600">Update personal info</p>
    </a>
</div>

<!-- Recent Results -->
@if(count($data['recentResults']) > 0)
<div class="card">
    <div class="card-header">
        <h2 class="text-lg font-semibold text-gray-900">My Recent Results</h2>
        <span class="text-sm text-gray-600">Latest 5 subjects</span>
    </div>
    <div class="card-body">
        <div class="space-y-3">
            @forelse($data['recentResults'] as $result)
                <div class="flex items-center justify-between p-4 border-2 border-gray-200 rounded-lg hover:border-primary-400 transition-colors">
                    <div>
                        <p class="font-semibold text-gray-900">{{ $result['subject'] }}</p>
                        <p class="text-sm text-gray-600">{{ $result['term'] }} Term - {{ $result['date'] }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-primary-600">{{ $result['score'] }}</p>
                        <p class="text-xs text-gray-500">Score</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-3"></i>
                    <p>No results available yet</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endif

<!-- Performance Tips -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
        <h3 class="font-semibold text-blue-900 flex items-center">
            <i class="fas fa-lightbulb mr-2"></i> Performance Tip
        </h3>
        <p class="text-blue-700 mt-2 text-sm">Focus on improving your scores in subjects where you're below average. Consistent study helps!</p>
    </div>
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
        <h3 class="font-semibold text-green-900 flex items-center">
            <i class="fas fa-check-circle mr-2"></i> Keep It Up
        </h3>
        <p class="text-green-700 mt-2 text-sm">You're doing well! Continue with your current study routine and you'll achieve your goals.</p>
    </div>
</div>
