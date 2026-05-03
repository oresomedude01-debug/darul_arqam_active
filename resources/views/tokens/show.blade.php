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

                <button onclick="printToken()" class="btn btn-outline">
                    <i class="fas fa-print mr-2"></i>
                    Print Token
                </button>

                <button onclick="shareToken()" class="btn btn-outline">
                    <i class="fas fa-share-alt mr-2"></i>
                    Share Token
                </button>

                <button onclick="copyToClipboard('{{ $token->code }}')" class="btn btn-outline">
                    <i class="fas fa-copy mr-2"></i>
                    Copy Token
                </button>
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

function printToken() {
    const tokenCode     = @json($token->code);
    const tokenStatus   = @json(ucfirst($token->status));
    const createdAt     = @json($token->created_at->format('M d, Y'));
    const expiresAt     = @json($token->expires_at ? $token->expires_at->format('M d, Y') : null);
    const sessionYear   = @json($token->session_year ?? 'Not specified');
    const className     = @json($token->schoolClass ? $token->schoolClass->name : 'Any Class');
    const schoolName    = @json(config('app.name', 'Darul Arqam School'));
    const siteUrl       = @json(request()->getSchemeAndHttpHost());
    const generatedAt   = @json(now()->format('M d, Y \\a\\t h:i A'));

    const expiresLine = expiresAt
        ? `<div class="detail-row"><span class="label">Expires:</span> <span>${expiresAt}</span></div>`
        : `<div class="detail-row"><span class="label">Expiry:</span> <span style="color:#16a34a;font-weight:bold;">No Expiry</span></div>`;

    const html = `<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registration Token - ${tokenCode}</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

  body {
    font-family: 'Inter', Arial, sans-serif;
    background: #fff;
    color: #1e293b;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  /* ---- PAGE 1: TOKEN ---- */
  .page {
    width: 210mm;
    min-height: 297mm;
    padding: 16mm 18mm;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    page-break-after: always;
    break-after: page;
    background: #fff;
  }
  .page:last-child {
    page-break-after: auto;
    break-after: auto;
  }

  /* HEADER */
  .school-header {
    text-align: center;
    padding-bottom: 6mm;
    border-bottom: 3px solid #6366f1;
    margin-bottom: 8mm;
    flex-shrink: 0;
  }
  .school-name {
    font-size: 20pt;
    font-weight: 800;
    color: #1e293b;
    letter-spacing: -0.5px;
  }
  .school-sub {
    font-size: 10pt;
    color: #6366f1;
    font-weight: 600;
    margin-top: 1mm;
    text-transform: uppercase;
    letter-spacing: 2px;
  }

  /* TOKEN CARD */
  .token-wrapper {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
  }
  .token-card {
    width: 100%;
    max-width: 150mm;
    border: 3px solid #6366f1;
    border-radius: 12px;
    padding: 12mm 14mm;
    text-align: center;
    background: #f8f7ff;
  }
  .token-label {
    font-size: 10pt;
    font-weight: 700;
    color: #6366f1;
    letter-spacing: 3px;
    text-transform: uppercase;
    margin-bottom: 6mm;
  }
  .token-code-box {
    background: #fff;
    border: 2px dashed #6366f1;
    border-radius: 8px;
    padding: 8mm 10mm;
    margin-bottom: 6mm;
  }
  .token-code {
    font-family: 'Courier New', 'Courier', monospace;
    font-size: 32pt;
    font-weight: 900;
    color: #4f46e5;
    letter-spacing: 5px;
    line-height: 1.1;
    word-break: break-all;
  }
  .token-hint {
    font-size: 8pt;
    color: #94a3b8;
    margin-top: 3mm;
    font-style: italic;
  }
  .token-details {
    margin-top: 5mm;
    display: flex;
    flex-direction: column;
    gap: 2mm;
    font-size: 9.5pt;
  }
  .detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 1.5mm;
    padding-top: 1.5mm;
  }
  .label {
    font-weight: 700;
    color: #6366f1;
  }

  /* HOW TO ENROLL */
  .enroll-section {
    margin-top: 8mm;
    flex-shrink: 0;
    background: #f1f5f9;
    border-radius: 8px;
    padding: 6mm 8mm;
  }
  .enroll-title {
    font-size: 10pt;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 4mm;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 1.5px;
  }
  .enroll-steps {
    list-style: none;
    counter-reset: step;
    font-size: 9pt;
    color: #334155;
    line-height: 1.6;
  }
  .enroll-steps li {
    counter-increment: step;
    display: flex;
    gap: 3mm;
    align-items: flex-start;
    margin-bottom: 2mm;
  }
  .enroll-steps li::before {
    content: counter(step);
    background: #6366f1;
    color: #fff;
    font-size: 8pt;
    font-weight: 700;
    min-width: 5mm;
    height: 5mm;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 0.5mm;
  }

  /* PAGE FOOTER */
  .page-footer {
    margin-top: 6mm;
    text-align: center;
    border-top: 1px solid #e2e8f0;
    padding-top: 3mm;
    font-size: 8pt;
    color: #94a3b8;
    flex-shrink: 0;
  }
  .page-footer .site {
    font-weight: 700;
    color: #6366f1;
    font-size: 9pt;
  }

  /* ---- PAGE 2: RULES ---- */
  .rules-header {
    text-align: center;
    padding-bottom: 6mm;
    border-bottom: 3px solid #6366f1;
    margin-bottom: 8mm;
    flex-shrink: 0;
  }
  .rules-header h2 {
    font-size: 16pt;
    font-weight: 800;
    color: #1e293b;
  }
  .rules-header p {
    font-size: 9pt;
    color: #64748b;
    margin-top: 1mm;
  }
  .rules-body {
    flex: 1;
    font-size: 9pt;
    color: #1e293b;
    line-height: 1.7;
  }
  .rules-intro {
    margin-bottom: 4mm;
    font-style: italic;
    color: #475569;
  }
  .rules-list {
    list-style: none;
    counter-reset: rule;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 2.5mm;
  }
  .rules-list li {
    counter-increment: rule;
    display: flex;
    gap: 3mm;
    padding: 2.5mm 3mm;
    background: #f8fafc;
    border-left: 3px solid #6366f1;
    border-radius: 0 6px 6px 0;
  }
  .rules-list li::before {
    content: counter(rule);
    font-size: 8pt;
    font-weight: 800;
    color: #6366f1;
    min-width: 4mm;
    flex-shrink: 0;
    padding-top: 0.5mm;
  }
  .rules-list li strong {
    color: #4f46e5;
  }
  .rules-acceptance {
    margin-top: 5mm;
    text-align: center;
    font-size: 9pt;
    font-weight: 700;
    color: #1e293b;
    padding: 3mm;
    border: 2px solid #6366f1;
    border-radius: 6px;
    background: #f1f5ff;
    flex-shrink: 0;
  }

  @media print {
    html, body { width: 210mm; }
    .page { margin: 0; box-shadow: none; }
  }
</style>
</head>
<body>

<!-- ========== PAGE 1: TOKEN ========== -->
<div class="page">
  <div class="school-header">
    <div class="school-name">${schoolName}</div>
    <div class="school-sub">Student Registration Token</div>
  </div>

  <div class="token-wrapper">
    <div class="token-card">
      <div class="token-label">&#127891; Registration Token</div>
      <div class="token-code-box">
        <div class="token-code">${tokenCode}</div>
        <div class="token-hint">Enter this code exactly as shown (case-sensitive)</div>
      </div>
      <div class="token-details">
        <div class="detail-row">
          <span class="label">Status:</span>
          <span style="font-weight:700;color:${tokenStatus==='Active'?'#16a34a':'#dc2626'};">${tokenStatus}</span>
        </div>
        <div class="detail-row">
          <span class="label">Session:</span>
          <span>${sessionYear}</span>
        </div>
        <div class="detail-row">
          <span class="label">Class:</span>
          <span>${className}</span>
        </div>
        <div class="detail-row">
          <span class="label">Issued:</span>
          <span>${createdAt}</span>
        </div>
        ${expiresLine}
      </div>
    </div>
  </div>

  <div class="enroll-section">
    <div class="enroll-title">&#128203; How to Enroll</div>
    <ol class="enroll-steps">
      <li>Visit the school website and click <strong>&ldquo;Enroll Now&rdquo;</strong></li>
      <li>Enter this <strong>token code</strong> exactly as printed above</li>
      <li>Complete all student and parent/guardian information</li>
      <li>Submit the form &mdash; enrollment is complete!</li>
    </ol>
  </div>

  <div class="page-footer">
    <div class="site">&#127760; ${siteUrl}</div>
    <div>Generated: ${generatedAt}</div>
  </div>
</div>

<!-- ========== PAGE 2: RULES ========== -->
<div class="page">
  <div class="rules-header">
    <h2>&#128209; Madrasah Rules &amp; Regulations</h2>
    <p>${schoolName} &mdash; Parent/Guardian Agreement</p>
  </div>

  <div class="rules-body">
    <p class="rules-intro">By enrolling a child in our Madrasah, parents/guardians agree to the following rules and conditions:</p>
    <ul class="rules-list">
      <li><strong>Fees &amp; Payments:</strong> All fees must be paid on time. Fees once paid are non-refundable. Outstanding fees must be cleared before promotion or withdrawal.</li>
      <li><strong>Attendance &amp; Punctuality:</strong> Regular attendance and punctuality are compulsory. Parents must inform the school of any planned or emergency absence.</li>
      <li><strong>Behaviour &amp; Discipline:</strong> Pupils must maintain good Islamic conduct at all times. The Madrasah reserves the right to discipline pupils when necessary.</li>
      <li><strong>Respect for Teachers &amp; Staff:</strong> Teachers and staff must be treated with respect. Concerns should be addressed through proper school channels &mdash; any complaint must be directed to the Director, not the teacher directly.</li>
      <li><strong>Communication:</strong> Parents should communicate with the school during approved hours using official communication methods only.</li>
      <li><strong>Dress Code:</strong> Pupils must wear the approved Madrasah uniform and dress according to Islamic standards at all times on school premises.</li>
      <li><strong>School Property &amp; Materials:</strong> Parents are responsible for providing required learning materials and for any damage caused by their ward to school property.</li>
      <li><strong>Health &amp; Safety:</strong> Parents must inform the school of any medical condition or allergy. Children who are unwell must not be sent to school.</li>
      <li><strong>Withdrawal:</strong> Withdrawal from the Madrasah must be communicated in writing with adequate notice. Fees already paid remain non-refundable.</li>
      <li><strong>School Authority:</strong> The Madrasah reserves the right to amend its policies, rules, and regulations as deemed necessary by the management.</li>
    </ul>
  </div>

  <div class="rules-acceptance">
    &#9989; Enrollment into this Madrasah signifies full acceptance of all the above rules and regulations.
  </div>

  <div class="page-footer" style="margin-top:4mm;">
    <div class="site">&#127760; ${siteUrl}</div>
    <div>Generated: ${generatedAt}</div>
  </div>
</div>

<script>
  window.onload = function() {
    window.focus();
    window.print();
    window.onafterprint = function() { window.close(); };
    // Fallback for browsers that don't support onafterprint
    setTimeout(function() {
      if (!window.closed) window.close();
    }, 30000);
  };
<\/script>
</body>
</html>`;

    const printWin = window.open('', '_blank', 'width=900,height=700,scrollbars=yes,resizable=yes');
    if (!printWin) {
        alert('Pop-up blocked! Please allow pop-ups for this site to print.');
        return;
    }
    printWin.document.open();
    printWin.document.write(html);
    printWin.document.close();
}

async function shareToken() {
    const tokenCode     = @json($token->code);
    const tokenStatus   = @json(ucfirst($token->status));
    const createdAt     = @json($token->created_at->format('M d, Y'));
    const expiresAt     = @json($token->expires_at ? $token->expires_at->format('M d, Y') : null);
    const sessionYear   = @json($token->session_year ?? 'Not specified');
    const className     = @json($token->schoolClass ? $token->schoolClass->name : 'Any Class');
    const schoolName    = @json(config('app.name', 'Darul Arqam School'));
    const siteUrl       = @json(request()->getSchemeAndHttpHost());
    const generatedAt   = @json(now()->format('M d, Y \\a\\t h:i A'));

    const expiresLine = expiresAt
        ? `<div class="detail-row"><span class="label">Expires:</span> <span>${expiresAt}</span></div>`
        : `<div class="detail-row"><span class="label">Expiry:</span> <span style="color:#16a34a;font-weight:bold;">No Expiry</span></div>`;

    if (typeof html2pdf === 'undefined') {
        Toast.info('Preparing PDF document...');
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
        script.onload = () => generateAndSharePDF();
        script.onerror = () => Toast.error('Failed to load PDF library. Please check your internet connection.');
        document.head.appendChild(script);
    } else {
        generateAndSharePDF();
    }

    function generateAndSharePDF() {
        const container = document.createElement('div');
        container.style.position = 'absolute';
        container.style.left = '-9999px';
        container.style.top = '0';
        container.style.width = '210mm'; // Set exact A4 width
        container.style.backgroundColor = '#ffffff';

        container.innerHTML = `
        <div id="pdf-content">
<style>
  #pdf-content { font-family: 'Inter', Arial, sans-serif; background:#fff; color:#1e293b; }
  #pdf-content * { box-sizing: border-box; }
  .pdf-page { width: 210mm; min-height: 297mm; padding: 16mm 18mm; background: #fff; page-break-after: always; }
  .school-header { text-align:center; padding-bottom:6mm; border-bottom:3px solid #6366f1; margin-bottom:8mm; }
  .school-name { font-size:20pt; font-weight:800; color:#1e293b; letter-spacing:-0.5px; }
  .school-sub { font-size:10pt; color:#6366f1; font-weight:600; margin-top:1mm; text-transform:uppercase; letter-spacing:2px; }
  .token-wrapper { display:flex; flex-direction:column; align-items:center; justify-content:center; padding-top: 10mm; padding-bottom: 10mm;}
  .token-card { width:100%; max-width:150mm; border:3px solid #6366f1; border-radius:12px; padding:12mm 14mm; text-align:center; background:#f8f7ff; margin: 0 auto; }
  .token-label { font-size:10pt; font-weight:700; color:#6366f1; letter-spacing:3px; text-transform:uppercase; margin-bottom:6mm; }
  .token-code-box { background:#fff; border:2px dashed #6366f1; border-radius:8px; padding:8mm 10mm; margin-bottom:6mm; }
  .token-code { font-family:'Courier New','Courier',monospace; font-size:32pt; font-weight:900; color:#4f46e5; letter-spacing:5px; line-height:1.1; word-break:break-all; }
  .token-hint { font-size:8pt; color:#94a3b8; margin-top:3mm; font-style:italic; }
  .token-details { margin-top:5mm; display:flex; flex-direction:column; gap:2mm; font-size:9.5pt; text-align: left;}
  .detail-row { display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e2e8f0; padding-bottom:1.5mm; padding-top:1.5mm; }
  .label { font-weight:700; color:#6366f1; }
  .enroll-section { margin-top:8mm; background:#f1f5f9; border-radius:8px; padding:6mm 8mm; }
  .enroll-title { font-size:10pt; font-weight:700; color:#1e293b; margin-bottom:4mm; text-align:center; text-transform:uppercase; letter-spacing:1.5px; }
  .enroll-steps { list-style:none; padding-left: 0; font-size:9pt; color:#334155; line-height:1.6; }
  .enroll-steps li { display:flex; gap:3mm; align-items:flex-start; margin-bottom:2mm; }
  .step-num { background:#6366f1; color:#fff; font-size:8pt; font-weight:700; min-width:5mm; height:5mm; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:0.5mm; }
  .page-footer { margin-top:6mm; text-align:center; border-top:1px solid #e2e8f0; padding-top:3mm; font-size:8pt; color:#94a3b8; }
  .page-footer .site { font-weight:700; color:#6366f1; font-size:9pt; }
  .rules-header { text-align:center; padding-bottom:6mm; border-bottom:3px solid #6366f1; margin-bottom:8mm; }
  .rules-header h2 { font-size:16pt; font-weight:800; color:#1e293b; margin:0; }
  .rules-header p { font-size:9pt; color:#64748b; margin-top:1mm; margin-bottom:0; }
  .rules-body { font-size:9pt; color:#1e293b; line-height:1.7; }
  .rules-intro { margin-bottom:4mm; font-style:italic; color:#475569; }
  .rules-list { list-style:none; padding:0; display:flex; flex-direction:column; gap:2.5mm; margin:0;}
  .rules-list li { display:flex; gap:3mm; padding:2.5mm 3mm; background:#f8fafc; border-left:3px solid #6366f1; border-radius:0 6px 6px 0; }
  .rule-num { font-size:8pt; font-weight:800; color:#6366f1; min-width:4mm; flex-shrink:0; padding-top:0.5mm; }
  .rules-list li strong { color:#4f46e5; }
  .rules-acceptance { margin-top:5mm; text-align:center; font-size:9pt; font-weight:700; color:#1e293b; padding:3mm; border:2px solid #6366f1; border-radius:6px; background:#f1f5ff; }
</style>
<div class="pdf-page">
  <div class="school-header">
    <div class="school-name">${schoolName}</div>
    <div class="school-sub">Student Registration Token</div>
  </div>
  <div class="token-wrapper">
    <div class="token-card">
      <div class="token-label">&#127891; Registration Token</div>
      <div class="token-code-box">
        <div class="token-code">${tokenCode}</div>
        <div class="token-hint">Enter this code exactly as shown (case-sensitive)</div>
      </div>
      <div class="token-details">
        <div class="detail-row"><span class="label">Status:</span> <span style="font-weight:700;color:${tokenStatus==='Active'?'#16a34a':'#dc2626'};">${tokenStatus}</span></div>
        <div class="detail-row"><span class="label">Session:</span> <span>${sessionYear}</span></div>
        <div class="detail-row"><span class="label">Class:</span> <span>${className}</span></div>
        <div class="detail-row"><span class="label">Issued:</span> <span>${createdAt}</span></div>
        ${expiresLine}
      </div>
    </div>
  </div>
  <div class="enroll-section">
    <div class="enroll-title">&#128203; How to Enroll</div>
    <ul class="enroll-steps">
      <li><span class="step-num">1</span> <div>Visit the school website and click <strong>&ldquo;Enroll Now&rdquo;</strong></div></li>
      <li><span class="step-num">2</span> <div>Enter this <strong>token code</strong> exactly as printed above</div></li>
      <li><span class="step-num">3</span> <div>Complete all student and parent/guardian information</div></li>
      <li><span class="step-num">4</span> <div>Submit the form &mdash; enrollment is complete!</div></li>
    </ul>
  </div>
  <div class="page-footer">
    <div class="site">&#127760; ${siteUrl}</div>
    <div>Generated: ${generatedAt}</div>
  </div>
</div>
<div class="html2pdf__page-break"></div>
<div class="pdf-page">
  <div class="rules-header">
    <h2>&#128209; Madrasah Rules &amp; Regulations</h2>
    <p>${schoolName} &mdash; Parent/Guardian Agreement</p>
  </div>
  <div class="rules-body">
    <p class="rules-intro">By enrolling a child in our Madrasah, parents/guardians agree to the following rules and conditions:</p>
    <ul class="rules-list">
      <li><span class="rule-num">1.</span> <div><strong>Fees &amp; Payments:</strong> All fees must be paid on time. Fees once paid are non-refundable. Outstanding fees must be cleared before promotion or withdrawal.</div></li>
      <li><span class="rule-num">2.</span> <div><strong>Attendance &amp; Punctuality:</strong> Regular attendance and punctuality are compulsory. Parents must inform the school of any planned or emergency absence.</div></li>
      <li><span class="rule-num">3.</span> <div><strong>Behaviour &amp; Discipline:</strong> Pupils must maintain good Islamic conduct at all times. The Madrasah reserves the right to discipline pupils when necessary.</div></li>
      <li><span class="rule-num">4.</span> <div><strong>Respect for Teachers &amp; Staff:</strong> Teachers and staff must be treated with respect. Concerns should be addressed through proper school channels &mdash; any complaint must be directed to the Director, not the teacher directly.</div></li>
      <li><span class="rule-num">5.</span> <div><strong>Communication:</strong> Parents should communicate with the school during approved hours using official communication methods only.</div></li>
      <li><span class="rule-num">6.</span> <div><strong>Dress Code:</strong> Pupils must wear the approved Madrasah uniform and dress according to Islamic standards at all times on school premises.</div></li>
      <li><span class="rule-num">7.</span> <div><strong>School Property &amp; Materials:</strong> Parents are responsible for providing required learning materials and for any damage caused by their ward to school property.</div></li>
      <li><span class="rule-num">8.</span> <div><strong>Health &amp; Safety:</strong> Parents must inform the school of any medical condition or allergy. Children who are unwell must not be sent to school.</div></li>
      <li><span class="rule-num">9.</span> <div><strong>Withdrawal:</strong> Withdrawal from the Madrasah must be communicated in writing with adequate notice. Fees already paid remain non-refundable.</div></li>
      <li><span class="rule-num">10.</span> <div><strong>School Authority:</strong> The Madrasah reserves the right to amend its policies, rules, and regulations as deemed necessary by the management.</div></li>
    </ul>
  </div>
  <div class="rules-acceptance">&#9989; Enrollment into this Madrasah signifies full acceptance of all the above rules and regulations.</div>
  <div class="page-footer" style="margin-top:4mm;">
    <div class="site">&#127760; ${siteUrl}</div>
    <div>Generated: ${generatedAt}</div>
  </div>
</div>
        </div>`;

        document.body.appendChild(container);

        const opt = {
            margin:       0,
            filename:     `token-${tokenCode}.pdf`,
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        html2pdf().set(opt).from(container).output('blob').then(async function(pdfBlob) {
            document.body.removeChild(container);

            const fileName = `token-${tokenCode}.pdf`;
            const file = new File([pdfBlob], fileName, { type: 'application/pdf' });

            if (navigator.share && navigator.canShare && navigator.canShare({ files: [file] })) {
                try {
                    await navigator.share({
                        title: `Registration Token — ${tokenCode}`,
                        text:  `Here is the ${schoolName} registration token: ${tokenCode}`,
                        files: [file],
                    });
                } catch (err) {
                    if (err.name !== 'AbortError') {
                        downloadPdfFallback(file, fileName);
                    }
                }
            } else {
                downloadPdfFallback(file, fileName);
            }
        }).catch(err => {
             document.body.removeChild(container);
             Toast.error('An error occurred while generating the PDF.');
        });
    }

    function downloadPdfFallback(file, fileName) {
        const url = URL.createObjectURL(file);
        const a = document.createElement('a');
        a.href = url;
        a.download = fileName;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        Toast.success('PDF downloaded successfully!');
    }
}
</script>
@endpush
@endsection
