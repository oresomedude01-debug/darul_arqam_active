@extends('layouts.spa')

@section('title', 'Token Details')

@section('breadcrumb')
    <span class="text-gray-400">Student Management</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('tokens.index') }}" class="text-gray-400 hover:text-gray-600">Registration Tokens</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Token Details</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Token Details</h1>
            <p class="text-gray-600 mt-1">View registration token information and usage</p>
        </div>
        <a href="{{ route('tokens.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to List
        </a>
    </div>

    <!-- Token Code Card -->
    <div class="card">
        <div class="card-body">
            <div class="text-center py-8">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-primary-100 mb-4">
                    <i class="fas fa-ticket-alt text-primary-600 text-3xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ $token->code }}</h2>
                <div class="flex items-center justify-center space-x-3">
                    @if($token->status === 'active')
                        <span class="badge badge-success text-lg px-4 py-2">
                            <i class="fas fa-check-circle mr-2"></i> Active
                        </span>
                    @elseif($token->status === 'consumed')
                        <span class="badge badge-info text-lg px-4 py-2">
                            <i class="fas fa-user-check mr-2"></i> Consumed
                        </span>
                    @elseif($token->status === 'expired')
                        <span class="badge badge-warning text-lg px-4 py-2">
                            <i class="fas fa-clock mr-2"></i> Expired
                        </span>
                    @else
                        <span class="badge badge-danger text-lg px-4 py-2">
                            <i class="fas fa-ban mr-2"></i> Disabled
                        </span>
                    @endif
                </div>

                <div class="mt-6">
                    <button onclick="copyToClipboard('{{ $token->code }}')" class="btn btn-primary">
                        <i class="fas fa-copy mr-2"></i>
                        Copy Token Code
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Token Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Basic Info -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
            </div>
            <div class="card-body space-y-4">
                <div>
                    <p class="text-sm text-gray-600">Session/Academic Year</p>
                    <p class="text-gray-900 font-medium mt-1">
                        {{ $token->session_year ?? 'Not specified' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-600">Class Level</p>
                    <p class="text-gray-900 font-medium mt-1">
                        @if($token->schoolClass)
                            <span class="badge badge-primary">{{ $token->schoolClass->name }}</span>
                        @else
                            <span class="text-gray-400">Any class</span>
                        @endif
                    </p>
                </div>

                @if($token->note)
                <div>
                    <p class="text-sm text-gray-600">Note/Description</p>
                    <p class="text-gray-900 mt-1">{{ $token->note }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Dates & Timeline -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">Timeline</h3>
            </div>
            <div class="card-body space-y-4">
                <div>
                    <p class="text-sm text-gray-600">Created On</p>
                    <p class="text-gray-900 font-medium mt-1">
                        {{ $token->created_at->format('F d, Y \a\t h:i A') }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $token->created_at->diffForHumans() }}
                    </p>
                </div>

                @if($token->expires_at)
                <div>
                    <p class="text-sm text-gray-600">Expires On</p>
                    <p class="font-medium mt-1 {{ $token->expires_at->isPast() ? 'text-red-600' : 'text-gray-900' }}">
                        {{ $token->expires_at->format('F d, Y \a\t h:i A') }}
                    </p>
                    <p class="text-xs {{ $token->expires_at->isPast() ? 'text-red-500' : 'text-gray-500' }} mt-1">
                        @if($token->expires_at->isPast())
                            Expired {{ $token->expires_at->diffForHumans() }}
                        @else
                            Expires {{ $token->expires_at->diffForHumans() }}
                        @endif
                    </p>
                </div>
                @else
                <div>
                    <p class="text-sm text-gray-600">Expiry</p>
                    <p class="text-gray-900 font-medium mt-1">
                        <span class="badge badge-success">No expiry</span>
                    </p>
                </div>
                @endif

                @if($token->consumed_at)
                <div>
                    <p class="text-sm text-gray-600">Consumed On</p>
                    <p class="text-gray-900 font-medium mt-1">
                        {{ $token->consumed_at->format('F d, Y \a\t h:i A') }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $token->consumed_at->diffForHumans() }}
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Usage Information -->
    @if($token->user && $token->user->profile)
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-user-graduate mr-2 text-primary-600"></i>
                Student Enrolled with This Token
            </h3>
        </div>
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center">
                        @if($token->user->profile->photo_path)
                            <img src="{{ asset($token->user->profile->photo_path) }}"
                                 alt="{{ $token->user->profile->full_name }}"
                                 class="w-full h-full rounded-full object-cover">
                        @else
                            <span class="text-2xl font-bold text-primary-600">
                                {{ substr($token->user->profile->first_name ?? '', 0, 1) }}{{ substr($token->user->profile->last_name ?? '', 0, 1) }}
                            </span>
                        @endif
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-gray-900">{{ $token->user->profile->full_name }}</p>
                        <p class="text-sm text-gray-600">{{ $token->user->profile->admission_number ?? '-' }}</p>
                        <div class="flex items-center space-x-2 mt-1">
                            @if($token->schoolClass)
                            <span class="badge badge-primary">{{ $token->schoolClass->name }}</span>
                            @endif
                            <span class="badge badge-{{ $token->user->profile->status === 'active' ? 'success' : 'warning' }}">
                                {{ ucfirst($token->user->profile->status ?? 'unknown') }}
                            </span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('students.show', $token->user->profile->id) }}" class="btn btn-primary">
                    <i class="fas fa-arrow-right mr-2"></i>
                    View Student Profile
                </a>
            </div>

            @if($token->consumed_by_ip)
            <div class="mt-4 pt-4 border-t border-gray-200">
                <p class="text-xs text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Enrolled from IP: <code class="bg-gray-100 px-2 py-1 rounded">{{ $token->consumed_by_ip }}</code>
                </p>
            </div>
            @endif
        </div>
    </div>
    @else
    <div class="card">
        <div class="card-body text-center py-8">
            <i class="fas fa-hourglass-half text-gray-400 text-4xl mb-3"></i>
            <p class="text-gray-600">This token has not been used yet</p>
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Actions</h3>
        </div>
        <div class="card-body">
            <div class="flex flex-wrap gap-3">
                @if($token->status === 'active' && !$token->user)
                    <form method="POST" action="{{ route('tokens.update', $token->id) }}" class="inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="disabled">
                        <button type="submit"
                                class="btn btn-danger"
                                onclick="return confirm('Are you sure you want to disable this token?')">
                            <i class="fas fa-ban mr-2"></i>
                            Disable Token
                        </button>
                    </form>
                @elseif($token->status === 'disabled')
                    <form method="POST" action="{{ route('tokens.update', $token->id) }}" class="inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="active">
                        <button type="submit"
                                class="btn btn-success"
                                onclick="return confirm('Are you sure you want to enable this token?')">
                            <i class="fas fa-check-circle mr-2"></i>
                            Enable Token
                        </button>
                    </form>
                @endif

                <button onclick="window.print()" class="btn btn-outline">
                    <i class="fas fa-print mr-2"></i>
                    Print
                </button>

                <button onclick="copyToClipboard('{{ $token->code }}')" class="btn btn-outline">
                    <i class="fas fa-copy mr-2"></i>
                    Copy Token
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Print Section (Hidden until print) -->
<div id="printSection" style="display: none;">
    <style>
        @media print {
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            html, body {
                width: 100%;
                height: auto;
                margin: 0;
                padding: 0;
            }
            
            body * {
                visibility: hidden;
            }
            
            #printSection,
            #printSection * {
                visibility: visible;
            }
            
            #printSection {
                position: static !important;
                left: auto !important;
                top: auto !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            .print-container {
                width: 100%;
                margin: 0;
                padding: 0;
                font-family: Arial, sans-serif;
                display: block;
            }
            
            .print-page {
                width: 210mm;
                min-height: 297mm;
                padding: 15mm;
                font-family: Arial, sans-serif;
                display: block;
                margin: 0;
                background: white;
                page-break-after: always;
                break-after: always;
            }
            
            .print-page-token {
                display: flex;
                flex-direction: column;
                justify-content: flex-start;
                align-items: center;
                text-align: center;
                min-height: 297mm;
            }
            
            .print-page-rules {
                display: flex;
                flex-direction: column;
                min-height: 297mm;
            }
            
            .print-header {
                text-align: center;
                margin-bottom: 8mm;
                border-bottom: 2px solid #667eea;
                padding-bottom: 3mm;
                flex-shrink: 0;
            }
            
            .print-header h1 {
                margin: 0;
                font-size: 16pt;
                font-weight: bold;
                color: #333;
            }
            
            .print-header p {
                margin: 1mm 0 0 0;
                font-size: 9pt;
                color: #666;
            }
            
            .token-card {
                border: 3px solid #667eea;
                border-radius: 8px;
                padding: 15mm;
                text-align: center;
                background: #f9f9f9;
                page-break-inside: avoid;
                break-inside: avoid;
                flex-shrink: 0;
                margin: 20mm 0;
            }
            
            .token-title {
                font-size: 13pt;
                font-weight: bold;
                color: #667eea;
                margin-bottom: 8mm;
                letter-spacing: 1px;
            }
            
            .token-code-box {
                border: 3px solid #667eea;
                padding: 12mm;
                background: white;
                margin-bottom: 6mm;
                page-break-inside: avoid;
                break-inside: avoid;
            }
            
            .token-code {
                font-size: 28pt;
                font-weight: bold;
                font-family: 'Courier New', monospace;
                letter-spacing: 3px;
                color: #667eea;
                word-break: break-all;
                line-height: 1.2;
            }
            
            .token-details {
                font-size: 9pt;
                color: #666;
                margin-top: 4mm;
                line-height: 1.4;
            }
            
            .rules-header {
                text-align: center;
                margin-bottom: 8mm;
                border-bottom: 2px solid #667eea;
                padding-bottom: 3mm;
                flex-shrink: 0;
            }
            
            .rules-header h2 {
                margin: 0;
                font-size: 13pt;
                font-weight: bold;
                color: #333;
            }
            
            .rules-content {
                flex: 1;
                text-align: left;
                font-size: 8.5pt;
                line-height: 1.5;
                overflow: visible;
                display: block;
            }
            
            .rules-intro {
                font-size: 8.5pt;
                line-height: 1.4;
                margin-bottom: 3mm;
                color: #333;
                page-break-inside: avoid;
                break-inside: avoid;
            }
            
            .rules-list {
                font-size: 8.2pt;
                line-height: 1.6;
                margin: 0;
                padding-left: 8mm;
                list-style-position: outside;
            }
            
            .rules-list li {
                margin-bottom: 2mm;
                color: #333;
                page-break-inside: avoid;
                break-inside: avoid;
            }
            
            .rules-list strong {
                color: #667eea;
            }
            
            .rules-footer {
                font-size: 8.5pt;
                line-height: 1.4;
                margin-top: 3mm;
                font-weight: bold;
                text-align: center;
                color: #333;
                border-top: 1px solid #ddd;
                padding-top: 2mm;
                page-break-inside: avoid;
                break-inside: avoid;
                flex-shrink: 0;
            }
            
            .print-footer {
                text-align: center;
                border-top: 1px solid #ddd;
                padding-top: 2mm;
                margin-top: auto;
                font-size: 8pt;
                color: #999;
                flex-shrink: 0;
            }
            
            .website-info {
                text-align: center;
                font-size: 9pt;
                color: #667eea;
                font-weight: bold;
                margin-bottom: 2mm;
            }
        }
    </style>

    <div class="print-container">
        <!-- PAGE 1: TOKEN -->
        <div class="print-page print-page-token">
            <!-- Header -->
            <div class="print-header">
                <h1>{{ config('app.name', 'Darul Arqam School') }}</h1>
                <p>Student Registration Token</p>
            </div>

            <!-- Token Card - Centered -->
            <div class="token-card">
                <div class="token-title">🎓 REGISTRATION TOKEN</div>
                <div class="token-code-box">
                    <div class="token-code">{{ $token->code }}</div>
                </div>
                <div class="token-details">
                    <div style="margin-bottom: 2mm;">
                        <span style="color: #667eea; font-weight: bold;">Status:</span> {{ ucfirst($token->status) }}
                    </div>
                    <div style="margin-bottom: 2mm;">
                        <span style="color: #667eea; font-weight: bold;">Created:</span> {{ $token->created_at->format('M d, Y') }}
                    </div>
                    @if($token->expires_at)
                    <div>
                        <span style="color: #667eea; font-weight: bold;">Expires:</span> {{ $token->expires_at->format('M d, Y') }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Instructions Section -->
            <div style="text-align: center; flex: 0 0 auto; margin-top: 10mm; page-break-inside: avoid; break-inside: avoid;">
                <h2 style="font-size: 11pt; font-weight: bold; color: #333; margin: 0 0 4mm 0;">📋 How to Enroll</h2>
                <ol style="font-size: 8.5pt; text-align: left; margin: 2mm auto; padding-left: 16mm; line-height: 1.5; max-width: 120mm;">
                    <li>Visit our website and click "Enroll Now"</li>
                    <li>Enter this token code (case-sensitive)</li>
                    <li>Complete all student and parent information</li>
                    <li>Submit the form to complete enrollment</li>
                </ol>
            </div>

            <!-- Footer -->
            <div class="print-footer">
                <div class="website-info">🌐 {{ parse_url(request()->getSchemeAndHttpHost())['host'] ?? request()->getSchemeAndHttpHost() }}</div>
                <div>Generated: {{ now()->format('M d, Y \a\t h:i A') }}</div>
            </div>
        </div>

        <!-- PAGE 2: RULES & REGULATIONS -->
        <div class="print-page print-page-rules">
            <!-- Rules Header -->
            <div class="rules-header">
                <h2>📋 Madrasah Rules & Regulations</h2>
            </div>

            <!-- Rules Content -->
            <div class="rules-content">
                <p class="rules-intro">
                    By enrolling a child in our Madrasah, parents/guardians agree to the following:
                </p>

                <ul class="rules-list">
                    <li><strong>Fees & Payments:</strong> All fees must be paid on time. Fees once paid are non-refundable. Outstanding fees must be cleared before promotion or withdrawal.</li>
                    
                    <li><strong>Attendance & Punctuality:</strong> Regular attendance and punctuality are compulsory. Parents must inform the school of any absence.</li>
                    
                    <li><strong>Behaviour & Discipline:</strong> Pupils must maintain good Islamic conduct. The Madrasah reserves the right to discipline pupils when necessary.</li>
                    
                    <li><strong>Respect for Teachers & Staff:</strong> Teachers and staff must be treated with respect. Concerns should be addressed through proper school channels. Any complaint should be directed to the Director, not the teacher.</li>
                    
                    <li><strong>Communication:</strong> Parents should communicate with the school during approved hours and follow official communication methods.</li>
                    
                    <li><strong>Dress Code:</strong> Pupils must wear the approved uniform and dress according to Islamic standards.</li>
                    
                    <li><strong>School Property & Materials:</strong> Parents are responsible for providing learning materials and for any damage to school property caused by their ward.</li>
                    
                    <li><strong>Health & Safety:</strong> Parents must inform the school of any medical condition. Sick children should not be sent to school.</li>
                    
                    <li><strong>Withdrawal:</strong> Withdrawal must be done in writing. Fees already paid remain non-refundable.</li>
                    
                    <li><strong>School Authority:</strong> The Madrasah reserves the right to amend its policies when necessary.</li>
                </ul>

                <div class="rules-footer">
                    Enrollment signifies full acceptance of these rules.
                </div>
            </div>

            <!-- Footer -->
            <div class="print-footer">
                <div class="website-info">🌐 {{ parse_url(request()->getSchemeAndHttpHost())['host'] ?? request()->getSchemeAndHttpHost() }}</div>
                <div>Generated: {{ now()->format('M d, Y \a\t h:i A') }}</div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        Toast.success('Token code copied to clipboard!');
    });
}

// Override default print behavior
document.addEventListener('DOMContentLoaded', function() {
    // Store original print function
    const originalPrint = window.print;
    
    // Override window.print
    window.print = function() {
        // Show print section
        const printSection = document.getElementById('printSection');
        if (printSection) {
            printSection.style.display = 'block';
        }
        
        // Call original print
        originalPrint.call(window);
        
        // Hide print section after print dialog closes
        setTimeout(function() {
            if (printSection) {
                printSection.style.display = 'none';
            }
        }, 500);
    };
});
</script>
@endpush
@endsection
