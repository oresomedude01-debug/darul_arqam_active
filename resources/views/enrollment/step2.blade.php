@extends('layouts.public')

@section('title', 'Academic Journey - Step 2 of 5')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

<div class="max-w-6xl mx-auto py-10 px-4 overflow-hidden" x-data="{ hasHistory: true }">
    
    <div id="stepper-animate" class="opacity-0 translate-y-4">
        @include('enrollment._stepper', [
            'currentStep' => 2,
            'stepTitle' => 'Academic History',
            'stepDescription' => 'Tracing previous educational paths'
        ])
    </div>

    <form action="{{ route('enrollment.process-step2') }}" method="POST" class="mt-12">
        @csrf

        {{-- Display validation errors --}}
        @if ($errors->any())
        <div class="mb-8">
            <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-6 shadow-soft">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-circle-exclamation text-red-600 text-2xl mt-1"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-red-900 mb-3">Please correct the following issues:</h3>
                        <ul class="space-y-2">
                            @foreach ($errors->all() as $error)
                                <li class="text-red-700 font-medium flex items-start gap-2">
                                    <span class="text-red-500 mt-1">•</span>
                                    <span>{{ $error }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            
            <div class="lg:col-span-5" id="left-panel">
                <div class="relative sticky top-10">
                    <h2 class="text-4xl font-black text-slate-900 leading-tight mb-6">
                        Past <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-indigo-600">Chapters.</span>
                    </h2>
                    <p class="text-slate-500 text-lg mb-10 leading-relaxed">
                        Educational continuity helps us tailor our learning approach to the student's background.
                    </p>

                    <div class="bg-white border border-slate-100 p-6 rounded-[2rem] shadow-xl shadow-slate-200/50 relative overflow-hidden group">
                        <div class="flex items-center justify-between relative z-10">
                            <div>
                                <h4 class="font-bold text-slate-800">First Enrollment?</h4>
                                <p class="text-xs text-slate-400">Skip if this is the very first school.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="hasHistory" class="sr-only peer">
                                <div class="w-14 h-8 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-brand-600"></div>
                            </label>
                        </div>
                        <i class="fa-solid fa-graduation-cap absolute -right-4 -bottom-4 text-6xl text-slate-50 group-hover:text-brand-50 transition-colors"></i>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7" id="right-panel">
                <div x-show="hasHistory" 
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 scale-95 translate-x-10"
                     x-transition:enter-end="opacity-100 scale-100 translate-x-0"
                     class="space-y-8">
                    
                    <div class="bg-white/80 backdrop-blur-xl border border-white rounded-[2.5rem] p-8 md:p-12 shadow-2xl relative">
                        
                        <div class="absolute left-10 top-24 bottom-24 w-0.5 bg-gradient-to-b from-brand-600 via-slate-200 to-transparent"></div>

                        <div class="relative pl-12 mb-10 group">
                            <div class="absolute left-[-5px] top-2 w-3 h-3 rounded-full bg-brand-600 ring-4 ring-brand-50"></div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Previous Institution</label>
                            <input type="text" name="previous_school_name" 
                                   class="w-full bg-slate-50 border-b-2 border-slate-100 px-0 py-3 text-xl font-bold text-slate-800 focus:border-brand-600 focus:bg-transparent transition-all outline-none placeholder:text-slate-200" 
                                   placeholder="Enter School Name">
                        </div>

                        <div class="relative pl-12 mb-10 group">
                            <div class="absolute left-[-5px] top-2 w-3 h-3 rounded-full bg-slate-200 group-within:bg-brand-400 transition-colors"></div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Location/Address</label>
                            <textarea name="previous_school_address" rows="2"
                                      class="w-full bg-transparent border-b-2 border-slate-100 px-0 py-2 text-slate-700 focus:border-brand-600 transition-all outline-none resize-none"
                                      placeholder="Where was it located?"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-12 relative pl-12 mb-10">
                            <div class="absolute left-[-5px] top-2 w-3 h-3 rounded-full bg-slate-200"></div>
                            <div class="group">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Last Grade</label>
                                <input type="text" name="previous_school_grade" 
                                       class="w-full bg-transparent border-b-2 border-slate-100 py-2 font-bold text-slate-800 focus:border-brand-600 outline-none transition-all"
                                       placeholder="e.g. Grade 5">
                            </div>
                            <div class="group">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Exit Year</label>
                                <input type="number" name="previous_school_year" 
                                       class="w-full bg-transparent border-b-2 border-slate-100 py-2 font-bold text-slate-800 focus:border-brand-600 outline-none transition-all"
                                       value="{{ date('Y') }}">
                            </div>
                        </div>

                        <div class="relative pl-12 group">
                            <div class="absolute left-[-5px] top-2 w-3 h-3 rounded-full bg-slate-200"></div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Reason for Departure</label>
                            <textarea name="previous_school_reason" rows="2"
                                      class="w-full bg-slate-50 rounded-2xl p-4 text-slate-700 focus:bg-white focus:ring-4 focus:ring-brand-50 border-2 border-transparent focus:border-brand-600 transition-all outline-none"
                                      placeholder="Briefly explain why the student is leaving..."></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-12">
                        <a href="{{ route('enrollment.step1') }}" class="group flex items-center gap-3 font-bold text-slate-400 hover:text-slate-900 transition-colors">
                            <div class="w-12 h-12 rounded-full border border-slate-200 flex items-center justify-center group-hover:border-slate-900">
                                <i class="fa-solid fa-arrow-left"></i>
                            </div>
                            Back
                        </a>
                        <button type="submit" class="bg-slate-900 text-white px-10 py-5 rounded-[2rem] font-bold shadow-2xl hover:bg-brand-700 transition-all flex items-center gap-4 group">
                            Next: Health Info
                            <i class="fa-solid fa-chevron-right text-xs group-hover:translate-x-2 transition-transform"></i>
                        </button>
                    </div>
                </div>

                <div x-show="!hasHistory" 
                     x-transition:enter="transition delay-300 duration-500"
                     x-transition:enter-start="opacity-0 translate-y-10"
                     class="text-center py-20 bg-slate-50 rounded-[3rem] border-2 border-dashed border-slate-200">
                    <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
                        <i class="fa-solid fa-sparkles text-brand-500 text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">Fresh Start!</h3>
                    <p class="text-slate-500 mb-8 max-w-xs mx-auto">This student is beginning their academic journey with us. No previous history required.</p>
                    <button type="submit" class="bg-brand-600 text-white px-12 py-4 rounded-full font-bold shadow-lg">Confirm & Continue</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Entrance Animations
        const tl = gsap.timeline();
        
        tl.to("#stepper-animate", { opacity: 1, y: 0, duration: 0.8, ease: "power4.out" })
          .from("#left-panel", { opacity: 0, x: -50, duration: 1, ease: "power3.out" }, "-=0.4")
          .from("#right-panel", { opacity: 0, x: 50, duration: 1, ease: "power3.out" }, "-=1");

        // Hover effect for inputs
        document.querySelectorAll('input, textarea').forEach(el => {
            el.addEventListener('focus', () => {
                gsap.to(el.previousElementSibling.previousElementSibling, { scale: 1.5, backgroundColor: "#2563eb", duration: 0.3 });
            });
            el.addEventListener('blur', () => {
                gsap.to(el.previousElementSibling.previousElementSibling, { scale: 1, backgroundColor: el.value ? "#2563eb" : "#e2e8f0", duration: 0.3 });
            });
        });
    });
</script>

<style>
    /* Premium Typography & Smoothing */
    input::placeholder, textarea::placeholder {
        font-weight: 500;
        letter-spacing: normal;
    }
</style>
@endsection