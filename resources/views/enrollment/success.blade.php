@extends('layouts.public')

@section('title', 'Enrollment Successful')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

<div class="max-w-4xl mx-auto py-12 px-4 overflow-hidden">
    
    <div id="success-header" class="text-center mb-12 opacity-0 translate-y-8">
        <div class="w-24 h-24 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl shadow-emerald-100/50 border-4 border-white">
            <i class="fa-solid fa-check text-4xl"></i>
        </div>
        <h1 class="text-4xl font-black text-slate-900 mb-2 tracking-tight">Enrollment Successful!</h1>
        <p class="text-slate-500 text-lg">Welcome to the <strong>{{ $schoolSettings->school_name ?? 'Our School' }}</strong> family.</p>
    </div>

    <div id="admission-card" class="opacity-0 scale-95 mb-12">
        <div class="bg-white border border-slate-200 rounded-[3rem] shadow-2xl overflow-hidden relative">
            <div class="absolute top-0 right-0 w-64 h-64 bg-brand-50 rounded-full -mr-32 -mt-32 opacity-50"></div>
            
            <div class="grid grid-cols-1 md:grid-cols-3">
                <div class="bg-slate-900 p-10 text-center flex flex-col items-center justify-center border-r border-slate-800">
                    <div class="w-32 h-32 rounded-3xl bg-white/10 border-2 border-white/20 overflow-hidden mb-4 p-1">
                        @php $photo = session('step1_data.photo_path'); @endphp
                        @if($photo)
                            <img src="{{ asset('storage/' . $photo) }}" class="w-full h-full object-cover rounded-2xl">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-500">
                                <i class="fa-solid fa-user-graduate text-4xl"></i>
                            </div>
                        @endif
                    </div>
                    <p class="text-[10px] font-black tracking-[0.2em] text-brand-400 uppercase">Official Student</p>
                    <h2 class="text-white font-bold text-lg leading-tight mt-2">{{ $studentName }}</h2>
                </div>

                <div class="md:col-span-2 p-10 relative">
                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Admission Number</p>
                            <p class="text-3xl font-mono font-black text-brand-600 tracking-tighter">{{ $admissionNumber }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Status</p>
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full uppercase">Provisionally Admitted</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6 pt-6 border-t border-slate-100">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Assigned Portal Email</p>
                            <p class="text-xs font-bold text-slate-700 break-all">{{ strtolower($admissionNumber) }}@{{ parse_url(config('app.url'), PHP_URL_HOST) ?? 'school.local' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Date Issued</p>
                            <p class="text-xs font-bold text-slate-700">{{ date('d M, Y') }}</p>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-dashed border-slate-200 flex items-center justify-between">
                        <i class="fa-solid fa-barcode text-4xl text-slate-300"></i>
                        <p class="text-[9px] text-slate-400 max-w-[200px] leading-tight text-right">
                            This is an automated digital confirmation. Present this number at the registrar's office to complete physical verification.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="next-steps" class="opacity-0 translate-y-8">
        <h3 class="text-xl font-black text-slate-900 mb-8 flex items-center gap-3">
            <i class="fa-solid fa-list-check text-brand-600"></i>
            What Happens Next?
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @php
                $steps = [
                    ['icon' => 'fa-clipboard-check', 'color' => 'brand', 'title' => 'Review', 'desc' => 'Admissions team will verify your data within 48 hours.'],
                    ['icon' => 'fa-envelope-open-text', 'color' => 'indigo', 'title' => 'Confirmation', 'desc' => 'An official letter will be sent to the guardian email provided.'],
                    ['icon' => 'fa-file-invoice-dollar', 'color' => 'emerald', 'title' => 'Fee Schedule', 'desc' => 'Payment instructions will be attached to your welcome email.'],
                    ['icon' => 'fa-calendar-star', 'color' => 'orange', 'title' => 'Orientation', 'desc' => 'Check your dashboard for the upcoming orientation dates.']
                ];
            @endphp

            @foreach($steps as $s)
            <div class="bg-white p-6 rounded-3xl border border-slate-100 hover:shadow-lg hover:border-brand-200 transition-all group">
                <div class="w-12 h-12 rounded-2xl bg-{{ $s['color'] }}-50 text-{{ $s['color'] }}-600 flex items-center justify-center mb-4 text-xl group-hover:scale-110 transition-transform">
                    <i class="fa-solid {{ $s['icon'] }}"></i>
                </div>
                <h4 class="font-bold text-slate-900 mb-1">{{ $s['title'] }}</h4>
                <p class="text-xs text-slate-500 leading-relaxed">{{ $s['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <div id="docs-panel" class="opacity-0 translate-y-8 mt-10 bg-slate-50 rounded-[2.5rem] p-10 border-2 border-dashed border-slate-200">
        <h4 class="font-black text-slate-800 uppercase tracking-widest text-xs mb-6 text-center">Prepare for Physical Verification</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach(['Birth Certificate', '2 Passport Photographs', 'Previous Report Cards', 'Proof of Residence', 'Immunization Records'] as $doc)
            <div class="flex items-center gap-3 bg-white p-3 rounded-xl border border-slate-100 shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                <span class="text-sm font-bold text-slate-600">{{ $doc }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <div id="actions" class="opacity-0 mt-12 flex flex-col md:flex-row items-center justify-center gap-6">
        <button onclick="window.print()" class="w-full md:w-auto bg-slate-900 text-white px-10 py-4 rounded-2xl font-bold shadow-xl hover:bg-brand-600 hover:-translate-y-1 transition-all flex items-center justify-center gap-3">
            <i class="fa-solid fa-print"></i>
            Print Admission Pass
        </button>
        <a href="{{ route('enrollment.token') }}" class="font-bold text-slate-400 hover:text-brand-600 transition-colors flex items-center gap-2">
            <i class="fa-solid fa-plus-circle"></i>
            Enroll Another Student
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tl = gsap.timeline({ defaults: { ease: "power4.out", duration: 1.2 }});
        
        tl.to("#success-header", { opacity: 1, y: 0 })
          .to("#admission-card", { opacity: 1, scale: 1 }, "-=0.8")
          .to("#next-steps", { opacity: 1, y: 0 }, "-=0.8")
          .to("#docs-panel", { opacity: 1, y: 0 }, "-=1")
          .to("#actions", { opacity: 1, duration: 1 }, "-=0.5");
    });
</script>

<style>
    @media print {
        #success-header, #next-steps, #docs-panel, #actions, .stepper-nav { display: none !important; }
        body { background: white; }
        #admission-card { opacity: 1 !important; scale: 1 !important; transform: none !important; }
        .max-w-4xl { max-width: 100% !important; padding: 0 !important; }
    }
</style>
@endsection