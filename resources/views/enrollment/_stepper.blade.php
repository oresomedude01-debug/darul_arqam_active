{{-- Multi-step Progress Stepper Component --}}
<div class="mb-12 relative">
    <div class="hidden md:block">
        <div class="flex items-start justify-between relative">
            
            <div class="absolute top-5 left-0 w-full h-0.5 bg-slate-200 -z-10 rounded-full"></div>

            @php
                $steps = [
                    1 => ['title' => 'Student Info', 'desc' => 'Basic details', 'icon' => 'fa-user'],
                    2 => ['title' => 'Previous School', 'desc' => 'Academic history', 'icon' => 'fa-school'],
                    3 => ['title' => 'Health Info', 'desc' => 'Medical details', 'icon' => 'fa-heart-pulse'],
                    4 => ['title' => 'Guardian Info', 'desc' => 'Contact details', 'icon' => 'fa-users'],
                    5 => ['title' => 'Review', 'desc' => 'Confirm & submit', 'icon' => 'fa-file-signature'],
                ];
            @endphp

            @foreach($steps as $stepNum => $step)
                <div class="flex-1 flex flex-col items-center group">
                    <div class="relative flex items-center justify-center">
                        
                        @if($stepNum > 1)
                            <div class="absolute right-1/2 w-full h-0.5 transition-all duration-500 {{ $currentStep >= $stepNum ? 'bg-brand-600' : 'bg-transparent' }} -z-10"></div>
                        @endif

                        <div @class([
                            'w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-500 border-2 shadow-sm',
                            'bg-brand-600 border-brand-600 text-white shadow-brand-200 ring-4 ring-brand-50' => $currentStep == $stepNum,
                            'bg-emerald-500 border-emerald-500 text-white' => $currentStep > $stepNum,
                            'bg-white border-slate-200 text-slate-400' => $currentStep < $stepNum,
                        ])>
                            @if($currentStep > $stepNum)
                                <i class="fa-solid fa-check text-sm"></i>
                            @else
                                <span class="text-sm font-bold font-heading">{{ $stepNum }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 text-center px-2">
                        <h4 @class([
                            'text-xs font-bold uppercase tracking-wider transition-colors',
                            'text-brand-600' => $currentStep == $stepNum,
                            'text-emerald-600' => $currentStep > $stepNum,
                            'text-slate-500' => $currentStep < $stepNum,
                        ])>
                            {{ $step['title'] }}
                        </h4>
                        <p class="text-[11px] text-slate-400 mt-0.5 leading-tight hidden lg:block">
                            {{ $step['desc'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="md:hidden bg-white p-4 rounded-2xl border border-slate-200 shadow-soft">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-brand-50 rounded-lg flex items-center justify-center text-brand-600">
                    <i class="fa-solid {{ $steps[$currentStep]['icon'] }}"></i>
                </div>
                <div>
                    <span class="block text-xs font-bold text-slate-400 uppercase tracking-tighter">Step {{ $currentStep }} of 5</span>
                    <h3 class="text-sm font-bold text-slate-800">{{ $steps[$currentStep]['title'] }}</h3>
                </div>
            </div>
            <div class="text-right">
                <span class="text-lg font-bold text-brand-600">{{ $currentStep * 20 }}%</span>
            </div>
        </div>
        
        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
            <div class="bg-gradient-to-r from-brand-600 to-brand-400 h-full rounded-full transition-all duration-700 ease-out"
                 style="width: {{ $currentStep * 20 }}%"></div>
        </div>
    </div>
</div>