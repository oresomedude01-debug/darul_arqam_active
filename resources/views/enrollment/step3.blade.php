@extends('layouts.public')

@section('title', 'Medical Profile — Step 3')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

<div class="max-w-6xl mx-auto py-12 px-6 overflow-hidden">
    
    <div id="stepper-anim" class="opacity-0">
        @include('enrollment._stepper', [
            'currentStep' => 3,
            'stepTitle' => 'Safety & Well-being',
            'stepDescription' => 'Establishing medical protocols'
        ])
    </div>

    <form action="{{ route('enrollment.process-step3') }}" method="POST" class="mt-16">
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
            
            <div class="lg:col-span-4 space-y-8" id="left-col">
                <div class="bg-rose-50 border border-rose-100 rounded-[2.5rem] p-8 relative overflow-hidden group">
                    <div class="relative z-10">
                        <div class="w-14 h-14 bg-rose-500 text-white rounded-2xl flex items-center justify-center shadow-lg mb-6 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-pills text-xl"></i>
                        </div>
                        <h3 class="text-xl font-black text-rose-900 leading-tight">Emergency Care Readiness.</h3>
                        <p class="text-rose-700/70 text-sm mt-4 leading-relaxed font-medium">
                            Providing accurate medical data ensures our on-site nursing staff can act decisively during critical moments.
                        </p>
                    </div>
                    <i class="fa-solid fa-heart-pulse absolute -right-4 -bottom-4 text-8xl text-rose-100 opacity-50"></i>
                </div>

                <div class="bg-emerald-900 rounded-[2.5rem] p-8 text-white shadow-2xl shadow-emerald-200">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-1.5 h-6 bg-emerald-400 rounded-full"></div>
                        <h4 class="font-bold text-sm uppercase tracking-widest">Protocol Check</h4>
                    </div>
                    <p class="text-emerald-100/80 text-sm leading-relaxed mb-6">
                        Does the student require a permanent <span class="text-emerald-300 font-bold">Individualized Education Program (IEP)</span>? Please detail this in the Special Needs section.
                    </p>
                    <i class="fa-solid fa-stethoscope text-emerald-800 text-4xl opacity-40"></i>
                </div>
            </div>

            <div class="lg:col-span-8 space-y-10" id="right-col">
                
                <div class="bg-white border border-slate-200 rounded-[3rem] p-10 shadow-soft" 
                     x-data="{ allergies: {{ isset($data['allergies']) ? json_encode($data['allergies']) : '[]' }}, newAllergy: '' }">
                    
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-900">
                                <i class="fa-solid fa-shield-virus"></i>
                            </div>
                            <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight">Allergy Log</h2>
                        </div>
                        <span class="text-[10px] font-black bg-rose-100 text-rose-600 px-3 py-1 rounded-full uppercase tracking-widest" 
                              x-show="allergies.length > 0">High Priority</span>
                    </div>

                    <div class="relative mb-6 group">
                        <input type="text" x-model="newAllergy"
                               @keydown.enter.prevent="if(newAllergy.trim()) { allergies.push(newAllergy.trim()); newAllergy = ''; }"
                               class="w-full bg-slate-50 border-2 border-transparent focus:border-emerald-500 focus:bg-white rounded-2xl px-6 py-5 font-bold text-slate-900 transition-all outline-none shadow-inner"
                               placeholder="Add Allergy (e.g. Peanuts, Latex) + Press Enter">
                        <button type="button" 
                                @click="if(newAllergy.trim()) { allergies.push(newAllergy.trim()); newAllergy = ''; }"
                                class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-slate-900 text-white rounded-xl flex items-center justify-center hover:bg-emerald-600 transition-colors">
                            <i class="fa-solid fa-plus text-xs"></i>
                        </button>
                    </div>

                    <div class="flex flex-wrap gap-3 min-h-[50px] items-center border-t border-slate-50 pt-6">
                        <template x-for="(allergy, index) in allergies" :key="index">
                            <div class="flex items-center gap-2 bg-slate-900 text-white px-5 py-2 rounded-full text-sm font-bold animate-fade-in group hover:bg-rose-600 transition-colors cursor-default">
                                <span x-text="allergy"></span>
                                <button type="button" @click="allergies.splice(index, 1)" class="hover:rotate-90 transition-transform">
                                    <i class="fa-solid fa-times text-[10px]"></i>
                                </button>
                                <input type="hidden" name="allergies[]" :value="allergy">
                            </div>
                        </template>
                        <p x-show="allergies.length === 0" class="text-slate-400 text-sm italic ml-2">No critical allergies reported.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-8">
                    @foreach([
                        ['medical_conditions', 'Chronic Conditions', 'Any long-term illnesses or disabilities...', 'fa-file-medical'],
                        ['medications', 'Active Medications', 'List dosage and frequency of current meds...', 'fa-capsules'],
                        ['special_needs', 'Special Accommodations', 'Educational or physical support required...', 'fa-hand-holding-heart']
                    ] as [$name, $title, $placeholder, $icon])
                    <div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-soft group hover:border-emerald-200 transition-colors">
                        <div class="flex items-center gap-3 mb-6">
                            <i class="fa-solid {{ $icon }} text-slate-300 group-hover:text-emerald-500 transition-colors"></i>
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">{{ $title }}</label>
                        </div>
                        <textarea name="{{ $name }}" rows="3" 
                                  class="w-full bg-slate-50 rounded-[1.5rem] p-6 text-slate-900 font-bold focus:bg-white focus:ring-4 focus:ring-emerald-50 border-2 border-transparent focus:border-emerald-500 transition-all outline-none resize-none shadow-inner"
                                  placeholder="{{ $placeholder }}">{{ old($name, $data[$name] ?? '') }}</textarea>
                    </div>
                    @endforeach
                </div>

                <div class="bg-slate-900 rounded-[2.5rem] p-1 shadow-xl overflow-hidden group">
                    <label class="flex items-center justify-between p-8 cursor-pointer select-none">
                        <div class="flex items-center gap-6">
                            <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center text-emerald-400">
                                <i class="fa-solid fa-file-signature text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="text-white font-bold text-lg">Emergency Medical Consent</h4>
                                <p class="text-slate-400 text-xs mt-1">Authorize the school to seek urgent treatment</p>
                            </div>
                        </div>
                        <input type="checkbox" name="emergency_medical_consent" value="1" 
                               {{ old('emergency_medical_consent', $data['emergency_medical_consent'] ?? false) ? 'checked' : '' }}
                               class="w-8 h-8 rounded-xl border-none text-emerald-500 focus:ring-emerald-500 focus:ring-offset-slate-900 cursor-pointer">
                    </label>
                </div>

                <div class="flex items-center justify-between pt-10">
                    <a href="{{ route('enrollment.step2') }}" class="group flex items-center gap-3 font-bold text-slate-400 hover:text-slate-900 transition-colors">
                        <div class="w-12 h-12 rounded-full border border-slate-200 flex items-center justify-center group-hover:border-slate-900 transition-colors">
                            <i class="fa-solid fa-arrow-left"></i>
                        </div>
                        Previous
                    </a>
                    <button type="submit" class="bg-emerald-600 text-white px-12 py-5 rounded-[2rem] font-bold shadow-2xl hover:bg-slate-900 hover:-translate-y-1 transition-all flex items-center gap-4">
                        Proceed to Guardian Info
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tl = gsap.timeline({ defaults: { ease: "power4.out", duration: 1.2 }});
        tl.to("#stepper-anim", { opacity: 1, y: 0 })
          .from("#left-col", { opacity: 0, x: -50 }, "-=0.8")
          .from("#right-col > div", { opacity: 0, y: 40, stagger: 0.15 }, "-=1");
    });
</script>

<style>
    .shadow-soft { box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.04); }
    .animate-fade-in { animation: fadeIn 0.3s ease forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
</style>
@endsection