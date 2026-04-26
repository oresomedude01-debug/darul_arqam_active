@extends('layouts.spa')

@section('content')
<div class="min-h-screen bg-gray-50 py-6 md:py-12 px-4 md:px-0">
    <div class="max-w-4xl mx-auto space-y-6 md:space-y-8">
        <!-- Page Header -->
        <div class="mb-8 md:mb-10">
            <h1 class="text-2xl md:text-4xl font-bold text-gray-900 mb-2 md:mb-3">Generate Student Bills</h1>
            <p class="text-gray-600 text-base md:text-lg">Create bills for students based on fee structure templates or generate individual bills</p>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 md:p-6 text-green-800 shadow-sm" x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" @click.outside="show = false">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-start gap-3">
                    <i class="fas fa-check-circle text-lg md:text-xl mt-0.5 flex-shrink-0"></i>
                    <span class="font-medium text-sm md:text-base">{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-green-600 hover:text-green-800 flex-shrink-0">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 md:p-6 text-red-800 shadow-sm" x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" @click.outside="show = false">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-circle text-lg md:text-xl mt-0.5 flex-shrink-0"></i>
                    <span class="font-medium text-sm md:text-base">{{ session('error') }}</span>
                </div>
                <button @click="show = false" class="text-red-600 hover:text-red-800 flex-shrink-0">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        @endif

        @if(session('warning'))
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 md:p-6 text-yellow-800 shadow-sm" x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" @click.outside="show = false">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle text-lg md:text-xl mt-0.5 flex-shrink-0"></i>
                    <span class="font-medium text-sm md:text-base">{{ session('warning') }}</span>
                </div>
                <button @click="show = false" class="text-yellow-600 hover:text-yellow-800 flex-shrink-0">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        @endif

        <!-- Tabs -->
        <div x-data="{ activeTab: 'bulk' }" class="bg-white rounded-lg shadow-sm">
            <!-- Tab Navigation -->
            <div class="flex flex-col md:flex-row gap-0 md:gap-1 border-b border-gray-200">
                <button @click="activeTab = 'bulk'" 
                        :class="activeTab === 'bulk' ? 'border-b-2 md:border-b-2 md:border-t-0 text-primary-600 bg-gray-50 md:bg-white' : 'border-transparent text-gray-600 hover:text-gray-900'"
                        class="flex-1 md:flex-none px-4 md:px-6 py-4 md:py-4 font-medium text-sm md:text-base border-b-2 md:border-b-2 transition-colors text-center md:text-left">
                    <i class="fas fa-layer-group mr-2 hidden md:inline"></i><span class="md:hidden">Bulk</span><span class="hidden md:inline">Bulk Generation</span>
                </button>
                <button @click="activeTab = 'individual'" 
                        :class="activeTab === 'individual' ? 'border-b-2 md:border-b-2 md:border-t-0 text-primary-600 bg-gray-50 md:bg-white' : 'border-transparent text-gray-600 hover:text-gray-900'"
                        class="flex-1 md:flex-none px-4 md:px-6 py-4 md:py-4 font-medium text-sm md:text-base border-b-2 md:border-b-2 transition-colors text-center md:text-left">
                    <i class="fas fa-user mr-2 hidden md:inline"></i><span class="md:hidden">Individual</span><span class="hidden md:inline">Individual Student</span>
                </button>
            </div>

            <!-- Tab Content -->
            <div class="p-4 md:p-8">
                <!-- Bulk Generation Tab -->
                <div x-show="activeTab === 'bulk'" x-transition class="space-y-6 md:space-y-8">
                    <form action="{{ route('billing.generate-bills') }}" method="POST" class="space-y-6 md:space-y-8" x-data="termFilter()">
                        @csrf

                        <!-- Validation Errors Alert -->
                        @if($errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 md:p-6">
                            <h4 class="font-semibold text-red-900 mb-3">Validation Errors:</h4>
                            <ul class="text-sm text-red-800 space-y-2">
                                @foreach($errors->all() as $error)
                                <li class="flex items-start gap-2">
                                    <span class="flex-shrink-0 mt-1">•</span>
                                    <span>{{ $error }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <!-- Academic Session -->
                        <div>
                            <label class="block text-sm md:text-base font-semibold text-gray-900 mb-2 md:mb-3">Academic Session <span class="text-red-600">*</span></label>
                            <select name="academic_session_id" @change="filterTerms()" x-model="selectedSession" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-base @error('academic_session_id') border-red-500 @enderror" required>
                                <option value="">-- Select Session --</option>
                                @foreach($sessions as $session)
                                <option value="{{ $session->id }}" @selected(old('academic_session_id') == $session->id)>
                                    {{ $session->session }}
                                </option>
                                @endforeach
                            </select>
                            @error('academic_session_id')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Academic Term -->
                        <div>
                            <label class="block text-sm md:text-base font-semibold text-gray-900 mb-2 md:mb-3">Academic Term <span class="text-red-600">*</span></label>
                            <select name="academic_term_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-base @error('academic_term_id') border-red-500 @enderror" required>
                                <option value="">-- Select Term --</option>
                                <template x-for="term in filteredTerms" :key="term.id">
                                    <option :value="term.id" x-text="term.name + ' (' + term.term + ')'"></option>
                                </template>
                            </select>
                            @error('academic_term_id')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- School Class -->
                        <div>
                            <label class="block text-sm md:text-base font-semibold text-gray-900 mb-2 md:mb-3">School Class <span class="text-red-600">*</span></label>
                            <select name="school_class_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-base @error('school_class_id') border-red-500 @enderror" required>
                                <option value="">-- Select Class --</option>
                                @foreach($classes as $class)
                                <option value="{{ $class->id }}" @selected(old('school_class_id') == $class->id)>
                                    {{ $class->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('school_class_id')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fee Structure Template -->
                        <div>
                            <label class="block text-sm md:text-base font-semibold text-gray-900 mb-2 md:mb-3">Fee Structure Template <span class="text-red-600">*</span></label>
                            <select name="fee_structure_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-base @error('fee_structure_id') border-red-500 @enderror" required>
                                <option value="">-- Select Template --</option>
                                @foreach($feeStructures as $structure)
                                <option value="{{ $structure->id }}" @selected(old('fee_structure_id') == $structure->id)>
                                    {{ $structure->name }} (₦{{ number_format($structure->total_amount, 2) }})
                                </option>
                                @endforeach
                            </select>
                            @error('fee_structure_id')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bill Due Date -->
                        <div>
                            <label class="block text-sm md:text-base font-semibold text-gray-900 mb-2 md:mb-3">Bill Due Date</label>
                            <input type="date" name="due_date" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-base @error('due_date') border-red-500 @enderror"
                                   value="{{ old('due_date') }}">
                            @error('due_date')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Info Box -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 md:p-6">
                            <h3 class="font-semibold text-blue-900 mb-3 text-sm md:text-base">⚠️ Important Information</h3>
                            <ul class="text-sm md:text-base text-blue-800 space-y-2">
                                <li class="flex gap-2">
                                    <span class="flex-shrink-0">•</span>
                                    <span>Bills will be generated for all active students in the selected class</span>
                                </li>
                                <li class="flex gap-2">
                                    <span class="flex-shrink-0">•</span>
                                    <span>Each student will receive one bill with the selected fee structure</span>
                                </li>
                                <li class="flex gap-2">
                                    <span class="flex-shrink-0">•</span>
                                    <span>Existing bills for this session/term will not be duplicated</span>
                                </li>
                                <li class="flex gap-2">
                                    <span class="flex-shrink-0">•</span>
                                    <span>Due date is optional</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 border-t border-gray-200 pt-6 md:pt-8">
                            <button type="submit" class="w-full sm:flex-none bg-primary-600 text-white px-6 md:px-8 py-3 md:py-3 rounded-lg hover:bg-primary-700 transition font-medium text-sm md:text-base">
                                <i class="fas fa-file-invoice mr-2"></i>Generate Bills
                            </button>
                            <a href="{{ route('billing.fee-structures.index') }}" class="w-full sm:flex-none bg-gray-200 text-gray-900 px-6 md:px-8 py-3 md:py-3 rounded-lg hover:bg-gray-300 transition font-medium text-sm md:text-base text-center">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Individual Student Tab -->
                <div x-show="activeTab === 'individual'" x-transition class="space-y-6 md:space-y-8" x-data="termFilter()">
                    <form action="{{ route('billing.generate-individual-bill') }}" method="POST" class="space-y-6 md:space-y-8">
                        @csrf

                        <!-- Validation Errors Alert -->
                        @if($errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 md:p-6">
                            <h4 class="font-semibold text-red-900 mb-3">Validation Errors:</h4>
                            <ul class="text-sm text-red-800 space-y-2">
                                @foreach($errors->all() as $error)
                                <li class="flex items-start gap-2">
                                    <span class="flex-shrink-0 mt-1">•</span>
                                    <span>{{ $error }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <!-- Select Student - Searchable -->
                        <div x-data="studentSearch()">
                            <label class="block text-sm md:text-base font-semibold text-gray-900 mb-2 md:mb-3">Select Student <span class="text-red-600">*</span></label>
                            <div class="relative">
                                <input 
                                    type="hidden" 
                                    name="student_id" 
                                    x-model="selected"
                                    :value="selected"
                                >
                                <input 
                                    type="text" 
                                    x-model="search"
                                    @input="filterStudents()"
                                    @click="open = true"
                                    @focus="open = true"
                                    @keydown.escape="open = false"
                                    placeholder="Search by name, admission number, or class..."
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-base @error('student_id') border-red-500 @enderror"
                                >
                                
                                <!-- Dropdown List -->
                                <div 
                                    x-show="open" 
                                    @click.outside="open = false"
                                    class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-2 max-h-64 overflow-y-auto shadow-lg"
                                    style="display: none;"
                                >
                                    <template x-for="student in filtered" :key="student.id">
                                        <button 
                                            type="button"
                                            @click="
                                                selected = student.id;
                                                search = student.display;
                                                open = false;
                                            "
                                            class="w-full text-left px-4 py-3 hover:bg-primary-50 border-b border-gray-100 last:border-b-0 transition text-sm md:text-base"
                                        >
                                            <div class="font-medium text-gray-900" x-text="student.name"></div>
                                            <div class="text-xs md:text-sm text-gray-600 mt-1">
                                                <span x-text="'Admission: ' + student.admission_number"></span> • 
                                                <span x-text="'Class: ' + student.class_name"></span>
                                            </div>
                                        </button>
                                    </template>
                                    
                                    <div x-show="filtered.length === 0" class="px-4 py-4 text-gray-500 text-center text-sm">
                                        <span x-show="search === ''">No students available</span>
                                        <span x-show="search !== ''">No students match your search</span>
                                    </div>
                                </div>
                            </div>
                            @error('student_id')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Academic Session -->
                        <div>
                            <label class="block text-sm md:text-base font-semibold text-gray-900 mb-2 md:mb-3">Academic Session <span class="text-red-600">*</span></label>
                            <select name="session_id" @change="filterTerms()" x-model="selectedSession" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-base @error('session_id') border-red-500 @enderror" required>
                                <option value="">-- Select Session --</option>
                                @foreach($sessions as $session)
                                <option value="{{ $session->id }}" @selected(old('session_id') == $session->id)>
                                    {{ $session->session }}
                                </option>
                                @endforeach
                            </select>
                            @error('session_id')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Academic Term -->
                        <div>
                            <label class="block text-sm md:text-base font-semibold text-gray-900 mb-2 md:mb-3">Academic Term <span class="text-red-600">*</span></label>
                            <select name="term_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-base @error('term_id') border-red-500 @enderror" required>
                                <option value="">-- Select Term --</option>
                                <template x-for="term in filteredTerms" :key="term.id">
                                    <option :value="term.id" x-text="term.name + ' (' + term.term + ')'"></option>
                                </template>
                            </select>
                            @error('term_id')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fee Structure -->
                        <div>
                            <label class="block text-sm md:text-base font-semibold text-gray-900 mb-2 md:mb-3">Fee Structure Template <span class="text-red-600">*</span></label>
                            <select name="fee_structure_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-base @error('fee_structure_id') border-red-500 @enderror" required>
                                <option value="">-- Select Fee Structure --</option>
                                @foreach($feeStructures ?? [] as $structure)
                                <option value="{{ $structure->id }}" @selected(old('fee_structure_id') == $structure->id)>
                                    {{ $structure->name }} (₦{{ number_format($structure->total_amount, 2) }})
                                </option>
                                @endforeach
                            </select>
                            @error('fee_structure_id')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description/Reason (Optional) -->
                        <div>
                            <label class="block text-sm md:text-base font-semibold text-gray-900 mb-2 md:mb-3">Description/Reason for Bill</label>
                            <textarea name="description" rows="4" 
                                      placeholder="e.g., Extra charges, Late registration fee, Additional subjects, etc."
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-base @error('description') border-red-500 @enderror resize-none">{{ old('description') }}</textarea>
                            @error('description')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bill Due Date -->
                        <div>
                            <label class="block text-sm md:text-base font-semibold text-gray-900 mb-2 md:mb-3">Bill Due Date</label>
                            <input type="date" name="due_date" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-base @error('due_date') border-red-500 @enderror"
                                   value="{{ old('due_date') }}">
                            @error('due_date')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Info Box -->
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 md:p-6">
                            <h3 class="font-semibold text-amber-900 mb-3 text-sm md:text-base">ℹ️ Information</h3>
                            <ul class="text-sm md:text-base text-amber-800 space-y-2">
                                <li class="flex gap-2">
                                    <span class="flex-shrink-0">•</span>
                                    <span>Create individual bills for extra charges or special cases</span>
                                </li>
                                <li class="flex gap-2">
                                    <span class="flex-shrink-0">•</span>
                                    <span>Select from existing fee structure templates</span>
                                </li>
                                <li class="flex gap-2">
                                    <span class="flex-shrink-0">•</span>
                                    <span>Description helps track why the bill was created</span>
                                </li>
                                <li class="flex gap-2">
                                    <span class="flex-shrink-0">•</span>
                                    <span>Due date is optional but recommended</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 border-t border-gray-200 pt-6 md:pt-8">
                            <button type="submit" class="w-full sm:flex-none bg-primary-600 text-white px-6 md:px-8 py-3 md:py-3 rounded-lg hover:bg-primary-700 transition font-medium text-sm md:text-base">
                                <i class="fas fa-plus mr-2"></i>Create Individual Bill
                            </button>
                            <a href="{{ route('billing.fee-structures.index') }}" class="w-full sm:flex-none bg-gray-200 text-gray-900 px-6 md:px-8 py-3 md:py-3 rounded-lg hover:bg-gray-300 transition font-medium text-sm md:text-base text-center">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function termFilter() {
    const termsData = {!! json_encode($terms->map(function($t) {
        return [
            'id' => $t->id,
            'name' => $t->name,
            'term' => $t->term,
            'session_id' => $t->academic_session_id
        ];
    })) !!};

    return {
        selectedSession: '',
        all: termsData,
        filteredTerms: termsData,
        
        filterTerms() {
            if (this.selectedSession === '') {
                this.filteredTerms = [];
            } else {
                this.filteredTerms = this.all.filter(term => 
                    term.session_id === parseInt(this.selectedSession)
                );
            }
        }
    };
}

function studentSearch() {
    const studentsData = {!! json_encode($students->map(function($s) {
        return [
            'id' => $s->id,
            'name' => $s->first_name . ' ' . $s->last_name,
            'admission_number' => $s->admission_number,
            'class_name' => $s->schoolClass?->name ?? 'N/A',
            'display' => $s->first_name . ' ' . $s->last_name . ' (' . $s->admission_number . ')'
        ];
    })) !!};

    return {
        open: false,
        search: '',
        selected: '',
        all: studentsData,
        filtered: studentsData,
        
        filterStudents() {
            const query = this.search.toLowerCase();
            
            if (query === '') {
                this.filtered = this.all;
            } else {
                this.filtered = this.all.filter(student => {
                    const text = (
                        student.name + ' ' + 
                        student.admission_number + ' ' + 
                        student.class_name
                    ).toLowerCase();
                    return text.includes(query);
                });
            }
        }
    };
}
</script>
                    @csrf

                    <!-- Validation Errors Alert -->
                    @if($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <h4 class="font-medium text-red-900 mb-2">Validation Errors:</h4>
                        <ul class="text-sm text-red-800 space-y-1">
                            @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Academic Session -->
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">Academic Session <span class="text-red-600">*</span></label>
                        <select name="academic_session_id" @change="filterTerms()" x-model="selectedSession" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('academic_session_id') border-red-500 @enderror" required>
                            <option value="">-- Select Session --</option>
                            @foreach($sessions as $session)
                            <option value="{{ $session->id }}" @selected(old('academic_session_id') == $session->id)>
                                {{ $session->session }}
                            </option>
                            @endforeach
                        </select>
                        @error('academic_session_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Academic Term -->
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">Academic Term <span class="text-red-600">*</span></label>
                        <select name="academic_term_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('academic_term_id') border-red-500 @enderror" required>
                            <option value="">-- Select Term --</option>
                            <template x-for="term in filteredTerms" :key="term.id">
                                <option :value="term.id" x-text="term.name + ' (' + term.term + ')'"></option>
                            </template>
                        </select>
                        @error('academic_term_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- School Class -->
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">School Class <span class="text-red-600">*</span></label>
                        <select name="school_class_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('school_class_id') border-red-500 @enderror" required>
                            <option value="">-- Select Class --</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}" @selected(old('school_class_id') == $class->id)>
                                {{ $class->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('school_class_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fee Structure Template -->
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">Fee Structure Template <span class="text-red-600">*</span></label>
                        <select name="fee_structure_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('fee_structure_id') border-red-500 @enderror" required>
                            <option value="">-- Select Template --</option>
                            @foreach($feeStructures as $structure)
                            <option value="{{ $structure->id }}" @selected(old('fee_structure_id') == $structure->id)>
                                {{ $structure->name }} (₦{{ number_format($structure->total_amount, 2) }})
                            </option>
                            @endforeach
                        </select>
                        @error('fee_structure_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bill Due Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">Bill Due Date</label>
                        <input type="date" name="due_date" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('due_date') border-red-500 @enderror"
                               value="{{ old('due_date') }}">
                        @error('due_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Info Box -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="font-medium text-blue-900 mb-2">⚠️ Important Information</h3>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li>• Bills will be generated for all active students in the selected class</li>
                            <li>• Each student will receive one bill with the selected fee structure</li>
                            <li>• Existing bills for this session/term will not be duplicated</li>
                            <li>• Due date is optional</li>
                        </ul>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-3 border-t pt-6">
                        <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition">
                            <i class="fas fa-file-invoice mr-2"></i>Generate Bills
                        </button>
                        <a href="{{ route('billing.fee-structures.index') }}" class="bg-gray-200 text-gray-900 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

            <!-- Individual Student Tab -->
            <div x-show="activeTab === 'individual'" x-transition class="space-y-6" x-data="termFilter()">
                <form action="{{ route('billing.generate-individual-bill') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Validation Errors Alert -->
                    @if($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <h4 class="font-medium text-red-900 mb-2">Validation Errors:</h4>
                        <ul class="text-sm text-red-800 space-y-1">
                            @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Select Student - Searchable -->
                    <div x-data="studentSearch()">
                        <label class="block text-sm font-medium text-gray-900 mb-2">Select Student <span class="text-red-600">*</span></label>
                        <div class="relative">
                            <input 
                                type="hidden" 
                                name="student_id" 
                                x-model="selected"
                                :value="selected"
                            >
                            <input 
                                type="text" 
                                x-model="search"
                                @input="filterStudents()"
                                @click="open = true"
                                @focus="open = true"
                                @keydown.escape="open = false"
                                placeholder="Search by name, admission number, or class..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('student_id') border-red-500 @enderror"
                            >
                            
                            <!-- Dropdown List -->
                            <div 
                                x-show="open" 
                                @click.outside="open = false"
                                class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 max-h-64 overflow-y-auto shadow-lg"
                                style="display: none;"
                            >
                                <template x-for="student in filtered" :key="student.id">
                                    <button 
                                        type="button"
                                        @click="
                                            selected = student.id;
                                            search = student.display;
                                            open = false;
                                        "
                                        class="w-full text-left px-4 py-2 hover:bg-primary-50 border-b border-gray-100 last:border-b-0 transition"
                                    >
                                        <div class="font-medium text-gray-900" x-text="student.name"></div>
                                        <div class="text-sm text-gray-600">
                                            <span x-text="'Admission: ' + student.admission_number"></span> • 
                                            <span x-text="'Class: ' + student.class_name"></span>
                                        </div>
                                    </button>
                                </template>
                                
                                <div x-show="filtered.length === 0" class="px-4 py-2 text-gray-500">
                                    <span x-show="search === ''">No students available</span>
                                    <span x-show="search !== ''">No students match your search</span>
                                </div>
                            </div>
                        </div>
                        @error('student_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Academic Session -->
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">Academic Session <span class="text-red-600">*</span></label>
                        <select name="session_id" @change="filterTerms()" x-model="selectedSession" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('session_id') border-red-500 @enderror" required>
                            <option value="">-- Select Session --</option>
                            @foreach($sessions as $session)
                            <option value="{{ $session->id }}" @selected(old('session_id') == $session->id)>
                                {{ $session->session }}
                            </option>
                            @endforeach
                        </select>
                        @error('session_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Academic Term -->
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">Academic Term <span class="text-red-600">*</span></label>
                        <select name="term_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('term_id') border-red-500 @enderror" required>
                            <option value="">-- Select Term --</option>
                            <template x-for="term in filteredTerms" :key="term.id">
                                <option :value="term.id" x-text="term.name + ' (' + term.term + ')'"></option>
                            </template>
                        </select>
                        @error('term_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fee Structure -->
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">Fee Structure Template <span class="text-red-600">*</span></label>
                        <select name="fee_structure_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('fee_structure_id') border-red-500 @enderror" required>
                            <option value="">-- Select Fee Structure --</option>
                            @foreach($feeStructures ?? [] as $structure)
                            <option value="{{ $structure->id }}" @selected(old('fee_structure_id') == $structure->id)>
                                {{ $structure->name }} (₦{{ number_format($structure->total_amount, 2) }})
                            </option>
                            @endforeach
                        </select>
                        @error('fee_structure_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description/Reason (Optional) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">Description/Reason for Bill</label>
                        <textarea name="description" rows="3" 
                                  placeholder="e.g., Extra charges, Late registration fee, Additional subjects, etc."
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('description') border-red-500 @enderror resize-none">{{ old('description') }}</textarea>
                        @error('description')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bill Due Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">Bill Due Date</label>
                        <input type="date" name="due_date" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('due_date') border-red-500 @enderror"
                               value="{{ old('due_date') }}">
                        @error('due_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Info Box -->
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                        <h3 class="font-medium text-amber-900 mb-2">ℹ️ Information</h3>
                        <ul class="text-sm text-amber-800 space-y-1">
                            <li>• Create individual bills for extra charges or special cases</li>
                            <li>• Select from existing fee structure templates</li>
                            <li>• Description helps track why the bill was created</li>
                            <li>• Due date is optional but recommended</li>
                        </ul>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-3 border-t pt-6">
                        <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition">
                            <i class="fas fa-plus mr-2"></i>Create Individual Bill
                        </button>
                        <a href="{{ route('billing.fee-structures.index') }}" class="bg-gray-200 text-gray-900 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function termFilter() {
    const termsData = {!! json_encode($terms->map(function($t) {
        return [
            'id' => $t->id,
            'name' => $t->name,
            'term' => $t->term,
            'session_id' => $t->academic_session_id
        ];
    })) !!};

    return {
        selectedSession: '',
        all: termsData,
        filteredTerms: termsData,
        
        filterTerms() {
            if (this.selectedSession === '') {
                this.filteredTerms = [];
            } else {
                this.filteredTerms = this.all.filter(term => 
                    term.session_id === parseInt(this.selectedSession)
                );
            }
        }
    };
}

function studentSearch() {
    const studentsData = {!! json_encode($students->map(function($s) {
        return [
            'id' => $s->id,
            'name' => $s->first_name . ' ' . $s->last_name,
            'admission_number' => $s->admission_number,
            'class_name' => $s->schoolClass?->name ?? 'N/A',
            'display' => $s->first_name . ' ' . $s->last_name . ' (' . $s->admission_number . ')'
        ];
    })) !!};

    return {
        open: false,
        search: '',
        selected: '',
        all: studentsData,
        filtered: studentsData,
        
        filterStudents() {
            const query = this.search.toLowerCase();
            
            if (query === '') {
                this.filtered = this.all;
            } else {
                this.filtered = this.all.filter(student => {
                    const text = (
                        student.name + ' ' + 
                        student.admission_number + ' ' + 
                        student.class_name
                    ).toLowerCase();
                    return text.includes(query);
                });
            }
        }
    };
}
</script>
@endsection
