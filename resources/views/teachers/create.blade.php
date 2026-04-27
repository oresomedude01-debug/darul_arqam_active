@extends('layouts.spa')

@section('title', 'Add Teacher')

@section('breadcrumb')
    <span class="text-gray-400">Teachers</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('teachers.index') }}" class="text-primary-600 hover:text-primary-700">All Teachers</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Add New</span>
@endsection

@section('content')
<div x-data="teacherForm()" class="max-w-5xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="fade-in-down flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Add New Teacher</h1>
            <p class="text-sm text-gray-600 mt-1">Fill in the teacher's information</p>
        </div>
        <a href="{{ route('teachers.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>

    <form action="{{ route('teachers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Basic Information -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-user mr-2 text-primary-600"></i>Basic Information
                </h3>
            </div>
            <div class="card-body space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Email <span class="text-red-500">*</span></label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="form-input @error('email') border-red-500 @enderror"
                            required
                            placeholder="teacher@school.com"
                        >
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password <span class="text-red-500">*</span></label>
                        <input
                            type="password"
                            name="password"
                            value="password123"
                            class="form-input @error('password') border-red-500 @enderror"
                            required
                            placeholder="Default: password123"
                        >
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirm Password <span class="text-red-500">*</span></label>
                        <input
                            type="password"
                            name="password_confirmation"
                            value="password123"
                            class="form-input @error('password_confirmation') border-red-500 @enderror"
                            required
                            placeholder="Confirm password"
                        >
                        @error('password_confirmation')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status <span class="text-red-500">*</span></label>
                        <select name="status" class="form-select @error('status') border-red-500 @enderror" required>
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">First Name <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            name="first_name"
                            value="{{ old('first_name') }}"
                            class="form-input @error('first_name') border-red-500 @enderror"
                            required
                        >
                        @error('first_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Last Name <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            name="last_name"
                            value="{{ old('last_name') }}"
                            class="form-input @error('last_name') border-red-500 @enderror"
                            required
                        >
                        @error('last_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Gender <span class="text-red-500">*</span></label>
                        <select name="gender" class="form-select @error('gender') border-red-500 @enderror" required>
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @error('gender')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Date of Birth</label>
                        <input
                            type="date"
                            name="date_of_birth"
                            value="{{ old('date_of_birth') }}"
                            class="form-input @error('date_of_birth') border-red-500 @enderror"
                            max="{{ date('Y-m-d') }}"
                        >
                        @error('date_of_birth')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Date Joined</label>
                        <input
                            type="date"
                            name="date_joined"
                            value="{{ old('date_joined') }}"
                            class="form-input @error('date_joined') border-red-500 @enderror"
                        >
                        @error('date_joined')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Qualification</label>
                        <input
                            type="text"
                            name="qualification"
                            value="{{ old('qualification') }}"
                            placeholder="e.g., B.Ed, M.Ed, NCE"
                            class="form-input @error('qualification') border-red-500 @enderror"
                        >
                        @error('qualification')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Profile Picture -->
                <div class="form-group">
                    <label class="form-label">Profile Picture</label>
                    <div class="flex items-center space-x-4">
                        <div x-show="imagePreview" class="flex-shrink-0">
                            <img :src="imagePreview" alt="Preview" class="w-24 h-24 rounded-full object-cover shadow-lg">
                        </div>
                        <div class="flex-1">
                            <input
                                type="file"
                                name="profile_picture"
                                accept="image/jpeg,image/png,image/jpg"
                                @change="previewImage($event)"
                                class="form-input @error('profile_picture') border-red-500 @enderror"
                            >
                            <p class="text-xs text-gray-500 mt-1">Max 2MB. Accepted formats: JPG, PNG</p>
                            @error('profile_picture')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-address-book mr-2 text-primary-600"></i>Contact Information
                </h3>
            </div>
            <div class="card-body space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Phone <span class="text-red-500">*</span></label>
                        <input
                            type="tel"
                            name="phone"
                            value="{{ old('phone') }}"
                            class="form-input @error('phone') border-red-500 @enderror"
                            required
                        >
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea
                        name="address"
                        rows="2"
                        class="form-textarea @error('address') border-red-500 @enderror"
                        placeholder="Street address"
                    >{{ old('address') }}</textarea>
                    @error('address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="form-group">
                        <label class="form-label">City</label>
                        <input
                            type="text"
                            name="city"
                            value="{{ old('city') }}"
                            class="form-input @error('city') border-red-500 @enderror"
                            placeholder="Lagos"
                        >
                        @error('city')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">State</label>
                        <input
                            type="text"
                            name="state"
                            value="{{ old('state') }}"
                            class="form-input @error('state') border-red-500 @enderror"
                            placeholder="Lagos"
                        >
                        @error('state')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Country</label>
                        <input
                            type="text"
                            name="country"
                            value="{{ old('country', 'Nigeria') }}"
                            class="form-input @error('country') border-red-500 @enderror"
                            placeholder="Nigeria"
                        >
                        @error('country')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Teaching Assignments -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-chalkboard mr-2 text-primary-600"></i>Teaching Assignments
                </h3>
            </div>
            <div class="card-body space-y-4">
                <div class="form-group">
                    <label class="form-label">Subjects</label>
                    <div x-data="{ showInput: false, newSubject: '' }">
                        <div class="space-y-2">
                            <div class="flex flex-wrap gap-2" id="subjectsContainer">
                                @foreach($allSubjects as $subject)
                                    <label class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-primary-50 rounded-lg cursor-pointer transition-all hover:shadow-md">
                                        <input type="checkbox" name="subjects[]" value="{{ $subject }}" class="form-checkbox text-primary-600 mr-2" {{ in_array($subject, old('subjects', [])) ? 'checked' : '' }}>
                                        <span class="text-sm">{{ $subject }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @error('subjects')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Classes</label>
                    <div x-data="{ showInput: false, newClass: '' }">
                        <div class="space-y-2">
                            <div class="flex flex-wrap gap-2" id="classesContainer">
                                @foreach($allClasses as $class)
                                    <label class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-primary-50 rounded-lg cursor-pointer transition-all hover:shadow-md">
                                        <input type="checkbox" name="classes[]" value="{{ $class }}" class="form-checkbox text-primary-600 mr-2" {{ in_array($class, old('classes', [])) ? 'checked' : '' }}>
                                        <span class="text-sm">{{ $class }}</span>
                                    </label>
                                @endforeach
                            </div>

                            
                        </div>
                    </div>
                    @error('classes')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-info-circle mr-2 text-primary-600"></i>Additional Information
                </h3>
            </div>
            <div class="card-body space-y-4">
                <div class="form-group">
                    <label class="form-label">Notes</label>
                    <textarea
                        name="notes"
                        rows="3"
                        class="form-textarea @error('notes') border-red-500 @enderror"
                        placeholder="Any additional notes about the teacher..."
                    >{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between gap-4 pb-4">
            <a href="{{ route('teachers.index') }}" class="btn btn-outline">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-2"></i>Save Teacher
            </button>
        </div>
    </form>
</div>

<script>
    function teacherForm() {
        return {
            imagePreview: null,

            previewImage(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imagePreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            }
        }
    }
</script>
@endsection
