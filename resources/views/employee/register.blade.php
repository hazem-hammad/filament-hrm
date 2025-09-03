<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Registration - {{ get_setting('app_name', 'HRM System') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Employee Registration</h1>
            <p class="mt-2 text-gray-600">Join {{ get_setting('app_name', 'HRM System') }} - Fill out all required information</p>
        </div>

        <!-- Main Form -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <form action="{{ route('employee.registration.submit') }}" method="POST" class="p-6">
                @csrf
                
                <!-- Personal Information Section -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-user text-blue-500 mr-2"></i>
                        Personal Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Work Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                Work Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Personal Email -->
                        <div>
                            <label for="personal_email" class="block text-sm font-medium text-gray-700 mb-1">
                                Personal Email
                            </label>
                            <input type="email" id="personal_email" name="personal_email" value="{{ old('personal_email') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('personal_email') border-red-500 @enderror">
                            @error('personal_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                                placeholder="+1234567890"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Business Phone -->
                        <div>
                            <label for="business_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                Business Phone
                            </label>
                            <input type="tel" id="business_phone" name="business_phone" value="{{ old('business_phone') }}"
                                placeholder="+1234567890"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('business_phone') border-red-500 @enderror">
                            @error('business_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Gender -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Gender <span class="text-red-500">*</span>
                            </label>
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" name="gender" value="male" {{ old('gender') === 'male' ? 'checked' : '' }} required
                                        class="mr-2 text-blue-500 focus:ring-blue-500">
                                    <span class="text-sm text-gray-700">Male</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="gender" value="female" {{ old('gender') === 'female' ? 'checked' : '' }} required
                                        class="mr-2 text-blue-500 focus:ring-blue-500">
                                    <span class="text-sm text-gray-700">Female</span>
                                </label>
                            </div>
                            @error('gender')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Marital Status -->
                        <div>
                            <label for="marital_status" class="block text-sm font-medium text-gray-700 mb-1">
                                Marital Status <span class="text-red-500">*</span>
                            </label>
                            <select id="marital_status" name="marital_status" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('marital_status') border-red-500 @enderror">
                                <option value="">Select Marital Status</option>
                                <option value="single" {{ old('marital_status') === 'single' ? 'selected' : '' }}>Single</option>
                                <option value="married" {{ old('marital_status') === 'married' ? 'selected' : '' }}>Married</option>
                            </select>
                            @error('marital_status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- National ID -->
                        <div>
                            <label for="national_id" class="block text-sm font-medium text-gray-700 mb-1">
                                National ID Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="national_id" name="national_id" value="{{ old('national_id') }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('national_id') border-red-500 @enderror">
                            @error('national_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date of Birth -->
                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">
                                Date of Birth
                            </label>
                            <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('date_of_birth') border-red-500 @enderror">
                            @error('date_of_birth')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                                Address
                            </label>
                            <textarea id="address" name="address" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact Section -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-phone text-red-500 mr-2"></i>
                        Emergency Contact (Optional)
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Emergency Contact Name -->
                        <div>
                            <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Contact Name
                            </label>
                            <input type="text" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('emergency_contact_name') border-red-500 @enderror">
                            @error('emergency_contact_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Emergency Contact Relation -->
                        <div>
                            <label for="emergency_contact_relation" class="block text-sm font-medium text-gray-700 mb-1">
                                Relationship
                            </label>
                            <input type="text" id="emergency_contact_relation" name="emergency_contact_relation" value="{{ old('emergency_contact_relation') }}"
                                placeholder="e.g., Spouse, Parent, Sibling"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('emergency_contact_relation') border-red-500 @enderror">
                            @error('emergency_contact_relation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Emergency Contact Phone -->
                        <div class="md:col-span-2">
                            <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                Contact Phone
                            </label>
                            <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}"
                                placeholder="+1234567890"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('emergency_contact_phone') border-red-500 @enderror">
                            @error('emergency_contact_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Company Information Section -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-building text-green-500 mr-2"></i>
                        Company Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Department -->
                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700 mb-1">
                                Department <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="department" name="department" value="{{ old('department') }}" required
                                placeholder="e.g., Engineering, Marketing"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('department') border-red-500 @enderror">
                            @error('department')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Position -->
                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700 mb-1">
                                Position <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="position" name="position" value="{{ old('position') }}" required
                                placeholder="e.g., Software Developer, Marketing Specialist"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('position') border-red-500 @enderror">
                            @error('position')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Employee Level -->
                        <div>
                            <label for="employee_level" class="block text-sm font-medium text-gray-700 mb-1">
                                Employee Level <span class="text-red-500">*</span>
                            </label>
                            <select id="employee_level" name="employee_level" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('employee_level') border-red-500 @enderror">
                                <option value="">Select Level</option>
                                <option value="intern" {{ old('employee_level') === 'intern' ? 'selected' : '' }}>Intern</option>
                                <option value="junior" {{ old('employee_level') === 'junior' ? 'selected' : '' }}>Junior</option>
                                <option value="mid" {{ old('employee_level') === 'mid' ? 'selected' : '' }}>Mid</option>
                                <option value="senior" {{ old('employee_level') === 'senior' ? 'selected' : '' }}>Senior</option>
                                <option value="lead" {{ old('employee_level') === 'lead' ? 'selected' : '' }}>Lead</option>
                                <option value="manager" {{ old('employee_level') === 'manager' ? 'selected' : '' }}>Manager</option>
                                <option value="director" {{ old('employee_level') === 'director' ? 'selected' : '' }}>Director</option>
                            </select>
                            @error('employee_level')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contract Type -->
                        <div>
                            <label for="contract_type" class="block text-sm font-medium text-gray-700 mb-1">
                                Contract Type <span class="text-red-500">*</span>
                            </label>
                            <select id="contract_type" name="contract_type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('contract_type') border-red-500 @enderror">
                                <option value="">Select Contract Type</option>
                                <option value="permanent" {{ old('contract_type') === 'permanent' ? 'selected' : '' }}>Permanent</option>
                                <option value="fulltime" {{ old('contract_type') === 'fulltime' ? 'selected' : '' }}>Full Time</option>
                                <option value="parttime" {{ old('contract_type') === 'parttime' ? 'selected' : '' }}>Part Time</option>
                                <option value="freelance" {{ old('contract_type') === 'freelance' ? 'selected' : '' }}>Freelance</option>
                                <option value="credit_hours" {{ old('contract_type') === 'credit_hours' ? 'selected' : '' }}>Credit Hours</option>
                                <option value="internship" {{ old('contract_type') === 'internship' ? 'selected' : '' }}>Internship</option>
                            </select>
                            @error('contract_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Manager Email -->
                        <div>
                            <label for="manager_email" class="block text-sm font-medium text-gray-700 mb-1">
                                Manager Email (Optional)
                            </label>
                            <input type="email" id="manager_email" name="manager_email" value="{{ old('manager_email') }}"
                                placeholder="manager@company.com"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('manager_email') border-red-500 @enderror">
                            @error('manager_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Company Joining Date -->
                        <div>
                            <label for="company_joining_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Joining Date (Optional)
                            </label>
                            <input type="date" id="company_joining_date" name="company_joining_date" value="{{ old('company_joining_date') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('company_joining_date') border-red-500 @enderror">
                            @error('company_joining_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Password Section -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-lock text-yellow-500 mr-2"></i>
                        Account Security
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="password" name="password" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Minimum 8 characters</p>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password Confirmation -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('password_confirmation') border-red-500 @enderror">
                            @error('password_confirmation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-center">
                    <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-8 rounded-lg shadow-lg transition duration-300 ease-in-out transform hover:scale-105">
                        <i class="fas fa-user-plus mr-2"></i>
                        Register as Employee
                    </button>
                </div>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mt-6 bg-red-50 border border-red-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    Please correct the following errors:
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-gray-500 text-sm">
            <p>&copy; {{ date('Y') }} {{ get_setting('app_name', 'HRM System') }}. All rights reserved.</p>
        </div>
    </div>

    <script>
        // Simple form validation feedback
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input[required], select[required]');
            
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.value.trim() === '') {
                        this.classList.add('border-red-500');
                    } else {
                        this.classList.remove('border-red-500');
                        this.classList.add('border-green-500');
                    }
                });
            });
        });
    </script>
</body>
</html>