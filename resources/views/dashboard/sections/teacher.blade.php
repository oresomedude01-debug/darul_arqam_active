<!-- Teacher Dashboard Stats -->
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
        <i class="fas fa-presentation text-emerald-600"></i>Teaching Workload
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($data['stats'] as $index => $stat)
            <div class="stat-card from-{{ $stat['color'] }}-500 to-{{ $stat['color'] }}-600" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-{{ $stat['color'] }}-100 text-sm font-medium">{{ $stat['label'] }}</p>
                    <h3 class="text-3xl font-bold mt-2">{{ $stat['value'] }}</h3>
                    <p class="text-{{ $stat['color'] }}-100 text-xs mt-2">
                        <i class="fas fa-check"></i> {{ $stat['trend'] }}
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

<!-- Teacher Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <a href="{{ route('teacher.results.classes') }}" class="group p-4 bg-indigo-50 border-2 border-indigo-200 rounded-lg hover:border-indigo-600 hover:bg-indigo-100 transition-all" data-aos="zoom-in" data-aos-delay="100">
        <i class="fas fa-plus-circle text-2xl text-indigo-600 mb-2 block"></i>
        <p class="font-semibold text-gray-900 text-sm">Enter Results</p>
        <p class="text-xs text-gray-600">Record student scores</p>
    </a>
    <a href="{{ route('teacher.mark-attendance') }}" class="group p-4 bg-cyan-50 border-2 border-cyan-200 rounded-lg hover:border-cyan-600 hover:bg-cyan-100 transition-all" data-aos="zoom-in" data-aos-delay="200">
        <i class="fas fa-clipboard-check text-2xl text-cyan-600 mb-2 block"></i>
        <p class="font-semibold text-gray-900 text-sm">Take Attendance</p>
        <p class="text-xs text-gray-600">Mark daily attendance</p>
    </a>
    <a href="{{ route('teacher.my-classes') }}" class="group p-4 bg-rose-50 border-2 border-rose-200 rounded-lg hover:border-rose-600 hover:bg-rose-100 transition-all" data-aos="zoom-in" data-aos-delay="300">
        <i class="fas fa-book text-2xl text-rose-600 mb-2 block"></i>
        <p class="font-semibold text-gray-900 text-sm">My Classes</p>
        <p class="text-xs text-gray-600">Manage class sections</p>
    </a>
</div>

<!-- Class Breakdown -->
@if($data['classBreakdown'])
<div class="card" data-aos="fade-up" data-aos-delay="200">
    <div class="card-header">
        <h2 class="text-lg font-semibold text-gray-900">My Classes - Current Term</h2>
    </div>
    <div class="card-body">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($data['classBreakdown'] as $class)
                <div class="p-4 border-2 border-gray-200 rounded-lg hover:border-primary-400 transition-colors">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-semibold text-gray-900">{{ $class['name'] }}</h3>
                        <span class="text-sm font-medium text-primary-600">{{ $class['results'] }}/{{ $class['students'] }}</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">Students: <strong>{{ $class['students'] }}</strong></p>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        @php
                            $percentage = $class['students'] > 0 ? round(($class['results'] / ($class['students'] * 5)) * 100) : 0;
                        @endphp
                        <div class="bg-primary-600 h-2 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Results: {{ $percentage }}% complete</p>
                </div>
            @empty
                <div class="col-span-2 text-center py-4 text-gray-500">No classes assigned</div>
            @endforelse
        </div>
    </div>
</div>
@endif

<!-- Recent Results -->
@if(count($data['recentResults']) > 0)
<div class="card" data-aos="fade-up" data-aos-delay="300">
    <div class="card-header">
        <h2 class="text-lg font-semibold text-gray-900">Recently Entered Results</h2>
        <span class="text-sm text-gray-600">Last 5 entries</span>
    </div>
    <div class="card-body">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b-2 border-gray-200">
                    <tr>
                        <th class="text-left py-3 px-4">Student</th>
                        <th class="text-left py-3 px-4">Subject</th>
                        <th class="text-left py-3 px-4">Class</th>
                        <th class="text-center py-3 px-4">Score</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['recentResults'] as $result)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 px-4 font-medium">{{ $result['student'] }}</td>
                            <td class="py-3 px-4">{{ $result['subject'] }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ $result['class'] }}</td>
                            <td class="py-3 px-4 text-center font-bold text-primary-600">{{ number_format($result['score'], 1) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-4 text-center text-gray-500">No results entered yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
