@extends('layouts.public')
@php
    $schoolSettings = \App\Models\SchoolSetting::getInstance();
@endphp



@section('title', 'Unlock Enrollment')

@section('content')
<div class="relative max-w-5xl mx-auto py-6 lg:py-12">
    
    <div class="absolute -top-20 -left-20 w-64 h-64 bg-brand-200/30 rounded-full blur-3xl -z-10"></div>
    <div class="absolute top-40 -right-20 w-72 h-72 bg-purple-200/30 rounded-full blur-3xl -z-10"></div>

    <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
        
        <div class="w-full lg:w-5/12 text-center lg:text-left">
            <div class="inline-flex mb-6 px-4 py-1.5 rounded-full bg-brand-50 border border-brand-100 items-center gap-2">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-600"></span>
                </span>
                <span class="text-xs font-bold text-brand-700 uppercase tracking-widest">Admissions Open 2026</span>
            </div>
            
            <h1 class="text-5xl lg:text-6xl font-heading font-black text-slate-900 leading-[1.1] mb-6">
                Start your <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-purple-600">Future</span> here.
            </h1>
            
            <p class="text-slate-500 text-lg leading-relaxed mb-8 max-w-md mx-auto lg:mx-0">
                To maintain a secure and personalized experience, please provide the registration token sent to your email or provided by the registrar.
            </p>

            <div class="grid grid-cols-2 gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-brand-600 border border-slate-100">
                        <i class="fa-solid fa-bolt-lightning text-xs"></i>
                    </div>
                    <span class="text-sm font-semibold text-slate-700">Instant Access</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-brand-600 border border-slate-100">
                        <i class="fa-solid fa-lock text-xs"></i>
                    </div>
                    <span class="text-sm font-semibold text-slate-700">Secure Portal</span>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-7/12">
            <div class="relative">
                <div class="absolute -inset-1 bg-gradient-to-r from-brand-600 to-purple-600 rounded-[2.5rem] blur opacity-20 group-hover:opacity-40 transition duration-1000"></div>
                
                <div class="relative bg-white/80 backdrop-blur-xl border border-white rounded-[2.5rem] shadow-2xl overflow-hidden">
                    <div class="p-8 md:p-12">
                        
                        <div class="mb-10 text-center lg:text-left">
                            <h2 class="text-2xl font-bold text-slate-800">Verify Identity</h2>
                            <p class="text-slate-400 text-sm">Enter your unique alphanumeric code</p>
                        </div>

                        @if($errors->any())
                            <div class="mb-8 animate-shake">
                                <div class="bg-red-50 border border-red-100 rounded-2xl p-4 flex gap-3 items-center">
                                    <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                                    <span class="text-sm font-medium text-red-800">{{ $errors->first() }}</span>
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('enrollment.validate-token') }}" method="POST" class="space-y-8">
                            @csrf
                            
                            <div class="relative">
                                <input type="text" 
                                       name="token_code" 
                                       placeholder="DAREG-XXXX-XXXX"
                                       class="peer w-full bg-slate-100/50 border-2 border-transparent rounded-2xl px-6 py-5 text-xl font-mono text-center tracking-[0.3em] uppercase transition-all focus:bg-white focus:border-brand-600 focus:ring-0 placeholder:font-sans placeholder:tracking-normal placeholder:text-slate-300"
                                       required>
                                
                                <div class="absolute -top-3 left-6 px-2 bg-white text-[10px] font-bold text-slate-400 uppercase tracking-widest peer-focus:text-brand-600 transition-colors">
                                    Access Token
                                </div>
                            </div>

                            <button type="submit" class="group relative w-full inline-flex items-center justify-center px-8 py-5 font-bold text-white transition-all duration-200 bg-slate-900 font-heading rounded-2xl hover:bg-slate-800 focus:outline-none">
                                <span class="relative flex items-center gap-2">
                                    Validate & Enter Portal
                                    <i class="fa-solid fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                                </span>
                            </button>
                        </form>

                        <div class="mt-10 flex flex-col sm:flex-row items-center justify-between gap-4 py-6 border-t border-slate-100">
                            <div class="flex -space-x-2">
                                @foreach([1,2,3,4] as $i)
                                    <img class="w-8 h-8 rounded-full border-2 border-white bg-slate-200" src="https://i.pravatar.cc/100?u={{$i}}" alt="User">
                                @endforeach
                                <div class="flex items-center justify-center w-8 h-8 rounded-full border-2 border-white bg-brand-600 text-[10px] text-white font-bold">
                                    +50
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-400 font-medium text-center sm:text-left">
                                Join 50+ students currently <br> in the enrollment process.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    .animate-shake { animation: shake 0.4s ease-in-out 0s 2; }
</style>
@endsection