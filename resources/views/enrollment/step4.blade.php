@extends('layouts.public')

@section('title', 'Guardian Information — Step 4')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

<div class="max-w-7xl mx-auto py-12 px-6" x-data="{ relationship: '{{ old('parent_relationship', $data['parent_relationship'] ?? '') }}' }">
    
    <div id="header-reveal" class="opacity-0">
        @include('enrollment._stepper', [
            'currentStep' => 4,
            'stepTitle' => 'Guardian Nexus',
            'stepDescription' => 'Primary contact and authority details'
        ])
    </div>

    <form action="{{ route('enrollment.process-step4') }}" method="POST" class="mt-16">
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
            
            <div class="lg:col-span-4" id="sidebar-reveal">
                <div class="sticky top-8 space-y-6">
                    <div class="bg-slate-900 rounded-[3rem] p-10 text-white shadow-2xl relative overflow-hidden">
                        <div class="relative z-10">
                            <div class="w-16 h-16 bg-brand-500 rounded-2xl flex items-center justify-center mb-8 shadow-lg shadow-brand-500/20">
                                <i class="fa-solid fa-users-viewfinder text-2xl"></i>
                            </div>
                            <h3 class="text-2xl font-black leading-tight mb-4">Authority & Communication</h3>
                            <p class="text-slate-400 text-sm leading-relaxed">
                                The primary guardian listed here will be the first point of contact for academic progress, emergencies, and financial billing.
                            </p>
                            
                            <div class="mt-8 pt-8 border-t border-slate-800 space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">SMS Alerts Enabled</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Official Email Recipient</span>
                                </div>
                            </div>
                        </div>
                        <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-brand-500/10 rounded-full blur-3xl"></div>
                    </div>

                    <div class="bg-amber-50 border border-amber-100 rounded-[2rem] p-6 flex items-start gap-4">
                        <i class="fa-solid fa-circle-exclamation text-amber-600 mt-1"></i>
                        <p class="text-xs text-amber-900 font-medium leading-relaxed">
                            Ensure the <strong class="font-black">Phone Number</strong> is currently active and reachable via WhatsApp for school group integration.
                        </p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-8 space-y-8" id="form-reveal">
                
                <div class="bg-white border border-slate-200 rounded-[3rem] p-10 md:p-14 shadow-soft relative overflow-hidden">
                    <div class="flex items-center justify-between mb-12">
                        <div>
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Primary Guardian</h2>
                            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Legal Representative</p>
                        </div>
                        <i class="fa-solid fa-shield-heart text-slate-100 text-6xl absolute right-10 top-10"></i>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-10">
                        
                        <div class="md:col-span-2 relative group">
                            <input type="text" name="parent_name" id="parent_name" 
                                   class="peer w-full bg-slate-50 border-2 border-transparent rounded-2xl px-6 py-5 text-slate-900 font-bold focus:bg-white focus:border-brand-600 transition-all outline-none placeholder-transparent shadow-inner @error('parent_name') border-rose-500 @enderror"
                                   placeholder="Full Name" value="{{ old('parent_name', $data['parent_name'] ?? '') }}" required>
                            <label for="parent_name" class="absolute left-6 top-5 text-slate-400 font-bold pointer-events-none transition-all peer-focus:-top-3 peer-focus:text-[10px] peer-focus:text-brand-600 peer-focus:bg-white peer-focus:px-2 peer-[:not(:placeholder-shown)]:-top-3 peer-[:not(:placeholder-shown)]:text-[10px] peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:px-2 uppercase tracking-widest">
                                Guardian Full Name *
                            </label>
                            @error('parent_name') <p class="text-rose-500 text-[10px] font-bold mt-2 ml-2 uppercase tracking-tighter">{{ $message }}</p> @enderror
                        </div>

                        <div class="relative group">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-2">Relationship *</label>
                            <div class="relative">
                                <select name="parent_relationship" x-model="relationship"
                                        class="w-full bg-slate-50 border-2 border-transparent rounded-2xl px-6 py-5 font-bold text-slate-900 focus:bg-white focus:border-brand-600 transition-all outline-none appearance-none shadow-inner" required>
                                    <option value="">Select Relation</option>
                                    @foreach(['Father', 'Mother', 'Guardian', 'Grandparent', 'Other'] as $rel)
                                        <option value="{{ $rel }}">{{ $rel }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <div class="relative group">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-2">Occupation</label>
                            <input type="text" name="parent_occupation" 
                                   class="w-full bg-slate-50 border-2 border-transparent rounded-2xl px-6 py-5 font-bold text-slate-900 focus:bg-white focus:border-brand-600 transition-all outline-none shadow-inner"
                                   placeholder="e.g. Software Engineer" value="{{ old('parent_occupation', $data['parent_occupation'] ?? '') }}">
                        </div>

                        <div class="relative group">
                            <input type="tel" name="parent_phone" id="parent_phone" 
                                   class="peer w-full bg-slate-50 border-2 border-transparent rounded-2xl px-6 py-5 text-slate-900 font-bold focus:bg-white focus:border-brand-600 transition-all outline-none placeholder-transparent shadow-inner"
                                   placeholder="Phone Number" value="{{ old('parent_phone', $data['parent_phone'] ?? '') }}" required>
                            <label for="parent_phone" class="absolute left-6 top-5 text-slate-400 font-bold pointer-events-none transition-all peer-focus:-top-3 peer-focus:text-[10px] peer-focus:text-brand-600 peer-focus:bg-white peer-focus:px-2 peer-[:not(:placeholder-shown)]:-top-3 peer-[:not(:placeholder-shown)]:text-[10px] peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:px-2 uppercase tracking-widest">
                                Mobile Number *
                            </label>
                        </div>

                        <div class="relative group">
                            <input type="email" name="parent_email" id="parent_email" 
                                   class="peer w-full bg-slate-50 border-2 border-transparent rounded-2xl px-6 py-5 text-slate-900 font-bold focus:bg-white focus:border-brand-600 transition-all outline-none placeholder-transparent shadow-inner"
                                   placeholder="Email Address" value="{{ old('parent_email', $data['parent_email'] ?? '') }}" required>
                            <label for="parent_email" class="absolute left-6 top-5 text-slate-400 font-bold pointer-events-none transition-all peer-focus:-top-3 peer-focus:text-[10px] peer-focus:text-brand-600 peer-focus:bg-white peer-focus:px-2 peer-[:not(:placeholder-shown)]:-top-3 peer-[:not(:placeholder-shown)]:text-[10px] peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:px-2 uppercase tracking-widest">
                                Email Address *
                            </label>
                        </div>

                    </div>
                </div>

                <div class="flex items-center justify-between pt-6">
                    <a href="{{ route('enrollment.step3') }}" class="group flex items-center gap-3 font-bold text-slate-400 hover:text-slate-900 transition-colors">
                        <div class="w-12 h-12 rounded-full border border-slate-200 flex items-center justify-center group-hover:bg-slate-50 transition-all">
                            <i class="fa-solid fa-arrow-left"></i>
                        </div>
                        Back to Health Info
                    </a>
                    
                    <button type="submit" class="relative overflow-hidden group bg-slate-900 text-white px-14 py-5 rounded-2xl font-black text-lg transition-all hover:shadow-2xl hover:shadow-brand-500/20 active:scale-95">
                        <span class="relative z-10 flex items-center gap-4">
                            Proceed to Final Step
                            <i class="fa-solid fa-circle-check text-brand-400"></i>
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-r from-brand-600 to-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tl = gsap.timeline({ defaults: { ease: "power4.out", duration: 1.2 }});
        tl.to("#header-reveal", { opacity: 1, y: 0 })
          .from("#sidebar-reveal", { opacity: 0, x: -40 }, "-=0.8")
          .from("#form-reveal", { opacity: 0, y: 40 }, "-=1");
    });
</script>

<style>
    .shadow-soft { box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.05); }
    select { cursor: pointer; }
</style>
@endsection