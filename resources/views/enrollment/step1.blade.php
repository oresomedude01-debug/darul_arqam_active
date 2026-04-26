@extends('layouts.public')

@section('title', 'Student Identity - Step 1 of 5')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

<div class="max-w-7xl mx-auto py-10 px-4 overflow-hidden">
    
    <div id="stepper-animate" class="opacity-0 translate-y-4">
        @include('enrollment._stepper', [
            'currentStep' => 1,
            'stepTitle' => 'Student Identity',
            'stepDescription' => 'Establishing basic student records'
        ])
    </div>

    <form action="{{ route('enrollment.process-step1') }}" 
          method="POST" 
          enctype="multipart/form-data" 
          x-data="{ preview: '{{ isset($data['photo_path']) ? asset('storage/' . $data['photo_path']) : '' }}' }"
          @submit="$form.submitBtn.disabled = true"
          class="mt-12">
        @csrf

        {{-- Display validation errors --}}
        @if ($errors->any())
        <div class="mb-8 max-w-7xl mx-auto px-4 lg:px-0">
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

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
            <div class="lg:col-span-3 space-y-6" id="left-panel">
                <div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-soft text-center relative overflow-hidden group">
                    <div class="relative z-10">
                        <div class="w-36 h-36 mx-auto mb-6 relative">
                            <div class="w-full h-full rounded-[2.5rem] bg-slate-100 overflow-hidden border-4 border-white ring-1 ring-slate-200 shadow-inner">
                                <template x-if="preview">
                                    <img :src="preview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!preview">
                                    <div class="w-full h-full flex items-center justify-center text-slate-300 bg-gradient-to-br from-slate-50 to-slate-100">
                                        <i class="fa-solid fa-user-astronaut text-5xl"></i>
                                    </div>
                                </template>
                            </div>
                            <label for="photo-upload" class="absolute -bottom-2 -right-2 w-12 h-12 bg-brand-600 text-white rounded-2xl flex items-center justify-center cursor-pointer hover:bg-slate-900 transition-all shadow-xl border-4 border-white">
                                <i class="fa-solid fa-camera"></i>
                            </label>
                            <input type="file" name="photo" id="photo-upload" class="hidden" @change="preview = URL.createObjectURL($event.target.files[0])">
                        </div>
                        <h3 class="font-black text-slate-900 tracking-tight">Student Photo</h3>
                        <p class="text-[10px] text-slate-400 uppercase tracking-widest mt-1">Official Passport Format</p>
                    </div>
                </div>

                <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden">
                    <h4 class="text-[10px] font-black uppercase tracking-[0.3em] mb-6 text-brand-400">Security Protocol</h4>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3 text-xs text-slate-300">
                            <i class="fa-solid fa-shield-check text-emerald-400 mt-0.5"></i>
                            <span>Data is encrypted using AES-256 standards.</span>
                        </li>
                        <li class="flex items-start gap-3 text-xs text-slate-300">
                            <i class="fa-solid fa-server text-emerald-400 mt-0.5"></i>
                            <span>Backend validation via institutional API.</span>
                        </li>
                    </ul>
                    <i class="fa-solid fa-fingerprint absolute -right-4 -bottom-4 text-7xl text-white opacity-5"></i>
                </div>
            </div>

            <div class="lg:col-span-9 space-y-10" id="right-panel">
                
                <div class="bg-white border border-slate-200 rounded-[3rem] p-10 shadow-soft relative overflow-hidden">
                    <div class="absolute left-10 top-24 bottom-10 w-0.5 bg-gradient-to-b from-brand-600 to-slate-100"></div>

                    <div class="flex items-center gap-4 mb-12 relative z-10">
                        <span class="w-12 h-12 bg-brand-50 text-brand-600 rounded-2xl flex items-center justify-center font-black">01</span>
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Personal Identity</h2>
                    </div>

                    <div class="pl-12 space-y-10">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            @foreach(['first_name' => 'First Name', 'middle_name' => 'Middle Name', 'last_name' => 'Last Name'] as $field => $label)
                            <div class="relative group">
                                <div class="absolute left-[-53px] top-6 w-3 h-3 rounded-full bg-slate-200 group-within:bg-brand-600 group-within:ring-4 group-within:ring-brand-50 transition-all"></div>
                                <input type="text" name="{{ $field }}" id="{{ $field }}" 
                                       class="peer w-full bg-slate-50 border-2 border-transparent rounded-2xl px-6 py-5 text-slate-900 font-bold focus:bg-white focus:border-brand-600 transition-all outline-none placeholder-transparent shadow-inner"
                                       placeholder="{{ $label }}" value="{{ old($field, $data[$field] ?? '') }}" {{ $field !== 'middle_name' ? 'required' : '' }}>
                                <label for="{{ $field }}" class="absolute left-6 top-5 text-slate-400 font-bold pointer-events-none transition-all peer-focus:-top-3 peer-focus:text-[10px] peer-focus:text-brand-600 peer-focus:bg-white peer-focus:px-2 peer-[:not(:placeholder-shown)]:-top-3 peer-[:not(:placeholder-shown)]:text-[10px] peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:px-2 uppercase tracking-widest">
                                    {{ $label }} {{ $field !== 'middle_name' ? '*' : '' }}
                                </label>
                            </div>
                            @endforeach
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="relative group">
                                <div class="absolute left-[-53px] top-12 w-3 h-3 rounded-full bg-slate-200 group-within:bg-brand-600 transition-all"></div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-2">Date of Birth *</label>
                                <input type="date" name="date_of_birth" class="w-full bg-slate-50 border-2 border-transparent rounded-2xl px-6 py-4 font-bold text-slate-900 focus:bg-white focus:border-brand-600 transition-all outline-none shadow-inner" value="{{ old('date_of_birth', $data['date_of_birth'] ?? '') }}" required>
                            </div>
                            <div class="relative group">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-2">Gender *</label>
                                <select name="gender" class="w-full bg-slate-50 border-2 border-transparent rounded-2xl px-6 py-4 font-bold text-slate-900 focus:bg-white focus:border-brand-600 transition-all outline-none appearance-none shadow-inner" required>
                                    <option value="">Select</option>
                                    <option value="male" {{ (old('gender', $data['gender'] ?? '') == 'male') ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ (old('gender', $data['gender'] ?? '') == 'female') ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div class="relative group">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-2">Blood Group</label>
                                <select name="blood_group" class="w-full bg-slate-50 border-2 border-transparent rounded-2xl px-6 py-4 font-bold text-slate-900 focus:bg-white focus:border-brand-600 transition-all outline-none appearance-none shadow-inner">
                                    <option value="">Select</option>
                                    @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                                        <option value="{{ $bg }}" {{ (old('blood_group', $data['blood_group'] ?? '') == $bg) ? 'selected' : '' }}>{{ $bg }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-[3rem] p-10 shadow-soft relative overflow-hidden">
                    <div class="absolute left-10 top-24 bottom-10 w-0.5 bg-slate-100"></div>
                    <div class="flex items-center gap-4 mb-12 relative z-10">
                        <span class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center font-black">02</span>
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Origin & Background</h2>
                    </div>

                    <div class="pl-12 grid grid-cols-1 md:grid-cols-3 gap-8">
                        @foreach(['nationality' => 'Nationality', 'religion' => 'Religion', 'place_of_birth' => 'Place of Birth'] as $field => $label)
                        <div class="relative group">
                            <div class="absolute left-[-53px] top-12 w-3 h-3 rounded-full bg-slate-200 group-within:bg-indigo-600 transition-all"></div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-2">{{ $label }}</label>
                            @if($field == 'religion')
                                <select name="religion" class="w-full bg-slate-50 border-2 border-transparent rounded-2xl px-6 py-4 font-bold text-slate-900 focus:bg-white focus:border-brand-600 transition-all outline-none shadow-inner">
                                    <option value="">Select</option>
                                    @foreach(['Islam', 'Christianity', 'Other'] as $r)
                                        <option value="{{ $r }}" {{ (old('religion', $data['religion'] ?? '') == $r) ? 'selected' : '' }}>{{ $r }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" name="{{ $field }}" class="w-full bg-slate-50 border-2 border-transparent rounded-2xl px-6 py-4 font-bold text-slate-900 focus:bg-white focus:border-brand-600 transition-all outline-none shadow-inner" value="{{ old($field, $data[$field] ?? ($field == 'nationality' ? 'Nigerian' : '')) }}">
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-[3rem] p-10 shadow-soft relative overflow-hidden">
                    <div class="flex items-center gap-4 mb-12 relative z-10">
                        <span class="w-12 h-12 bg-slate-900 text-white rounded-2xl flex items-center justify-center font-black">03</span>
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Residential Address</h2>
                    </div>

                    <div class="pl-12 space-y-8">
                        <div class="relative group">
                            <textarea name="address" id="address" rows="4" class="peer w-full bg-slate-50 border-2 border-transparent rounded-2xl px-6 py-5 text-slate-900 font-bold focus:bg-white focus:border-brand-600 transition-all outline-none placeholder-transparent shadow-inner" placeholder="Address">{{ old('address', $data['address'] ?? '') }}</textarea>
                            <label for="address" class="absolute left-6 top-5 text-slate-400 font-bold pointer-events-none transition-all peer-focus:-top-3 peer-focus:text-[10px] peer-focus:text-brand-600 peer-focus:bg-white peer-focus:px-2 peer-[:not(:placeholder-shown)]:-top-3 peer-[:not(:placeholder-shown)]:text-[10px] peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:px-2 uppercase tracking-widest">Residential Address</label>
                            <p class="text-xs text-slate-400 mt-2">Where the student resides</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-10">
                    <a href="{{ route('enrollment.token') }}" class="group flex items-center gap-3 font-bold text-slate-400 hover:text-slate-900 transition-colors">
                        <div class="w-12 h-12 rounded-full border border-slate-200 flex items-center justify-center group-hover:border-slate-900 group-hover:-translate-x-1 transition-all">
                            <i class="fa-solid fa-arrow-left"></i>
                        </div>
                        Back to Token
                    </a>
                    <button type="submit" class="bg-slate-900 text-white px-12 py-5 rounded-[2rem] font-black text-lg shadow-2xl hover:bg-brand-700 hover:-translate-y-1 transition-all flex items-center gap-4">
                        Proceed to Step 2
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tl = gsap.timeline({ defaults: { ease: "power4.out", duration: 1 }});
        
        tl.to("#stepper-animate", { opacity: 1, y: 0 })
          .from("#left-panel", { opacity: 0, x: -40 }, "-=0.6")
          .from("#right-panel", { opacity: 0, x: 40 }, "-=1")
          .from("#right-panel > div", { opacity: 0, y: 20, stagger: 0.15 }, "-=0.8");
    });
</script>
@endsection