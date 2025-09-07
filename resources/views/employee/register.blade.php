<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Registration - {{ get_setting('company_name', 'HRM System') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#eb4034',
                        'primary-50': '#fef2f2',
                        'primary-100': '#fee2e2',
                        'primary-600': '#dc2626',
                        'primary-700': '#b91c1c',
                    }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            @if(get_setting('logo_light'))
                <img src="{{ asset(get_setting('logo_light')) }}" alt="Company Logo" class="h-16 mx-auto mb-4">
            @endif
            <h1 class="text-4xl font-bold bg-gradient-to-r from-primary to-primary-600 bg-clip-text text-transparent">
                Employee Registration
            </h1>
            <p class="mt-3 text-lg text-gray-600">Join {{ get_setting('company_name', 'HRM System') }} - Fill out all required information</p>
        </div>

        <!-- Main Form -->
        <div class="bg-white shadow-2xl rounded-2xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-primary to-primary-600 p-6">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-user-plus mr-3"></i>
                    Employee Information
                </h2>
            </div>
            <form action="{{ route('employee.registration.submit') }}" method="POST" class="p-8">
                @csrf
                
                <!-- Personal Information Section -->
                <div class="mb-10">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 pb-3 border-b-2 border-primary/20 flex items-center">
                        <i class="fas fa-user text-primary mr-3 p-2 bg-primary/10 rounded-lg"></i>
                        Personal Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Full Name <span class="text-primary">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                placeholder="Enter employee name"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Work Email -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                Work Email <span class="text-primary">*</span>
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                placeholder="Enter employee work email"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 @error('email') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">A welcome email with login credentials will be sent after creation</p>
                            @error('email')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Personal Email -->
                        <div>
                            <label for="personal_email" class="block text-sm font-semibold text-gray-700 mb-2">
                                Personal Email
                            </label>
                            <input type="email" id="personal_email" name="personal_email" value="{{ old('personal_email') }}"
                                placeholder="Enter employee personal email"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 @error('personal_email') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Optional personal email address</p>
                            @error('personal_email')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                Phone Number <span class="text-primary">*</span>
                            </label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                                placeholder="Enter employee phone"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 @error('phone') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Please use with country code. (ex. +20)</p>
                            @error('phone')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Business Phone -->
                        <div>
                            <label for="business_phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                Business Phone
                            </label>
                            <input type="tel" id="business_phone" name="business_phone" value="{{ old('business_phone') }}"
                                placeholder="Enter business phone"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 @error('business_phone') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Optional business phone number</p>
                            @error('business_phone')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Gender -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Gender <span class="text-primary">*</span>
                            </label>
                            <div class="flex space-x-6">
                                <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="gender" value="male" {{ old('gender', 'male') === 'male' ? 'checked' : '' }} required
                                        class="mr-3 text-primary focus:ring-primary focus:ring-2 w-4 h-4">
                                    <span class="text-sm text-gray-700 group-hover:text-primary transition-colors">Male</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="gender" value="female" {{ old('gender') === 'female' ? 'checked' : '' }} required
                                        class="mr-3 text-primary focus:ring-primary focus:ring-2 w-4 h-4">
                                    <span class="text-sm text-gray-700 group-hover:text-primary transition-colors">Female</span>
                                </label>
                            </div>
                            @error('gender')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Marital Status -->
                        <div>
                            <label for="marital_status" class="block text-sm font-semibold text-gray-700 mb-2">
                                Marital Status <span class="text-primary">*</span>
                            </label>
                            <select id="marital_status" name="marital_status" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 @error('marital_status') border-red-500 @enderror">
                                <option value="">Select Marital Status</option>
                                <option value="single" {{ old('marital_status', 'single') === 'single' ? 'selected' : '' }}>Single</option>
                                <option value="married" {{ old('marital_status') === 'married' ? 'selected' : '' }}>Married</option>
                            </select>
                            @error('marital_status')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- National ID -->
                        <div>
                            <label for="national_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                National ID Number <span class="text-primary">*</span>
                            </label>
                            <input type="text" id="national_id" name="national_id" value="{{ old('national_id') }}" required
                                placeholder="Enter national ID number"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 @error('national_id') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Required national identification number</p>
                            @error('national_id')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date of Birth -->
                        <div>
                            <label for="date_of_birth" class="block text-sm font-semibold text-gray-700 mb-2">
                                Date of Birth <span class="text-primary">*</span>
                            </label>
                            <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', today()->toDateString()) }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 @error('date_of_birth') border-red-500 @enderror">
                            @error('date_of_birth')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
                                Address
                            </label>
                            <textarea id="address" name="address" rows="4"
                                placeholder="Enter employee address"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 resize-none @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                            @error('address')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact Section -->
                <div class="mb-10">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 pb-3 border-b-2 border-primary/20 flex items-center">
                        <i class="fas fa-phone text-primary mr-3 p-2 bg-primary/10 rounded-lg"></i>
                        Emergency Contact
                        <span class="ml-auto text-sm font-normal text-gray-500">Optional</span>
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Emergency Contact Name -->
                        <div>
                            <label for="emergency_contact_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Contact Name
                            </label>
                            <input type="text" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}"
                                placeholder="Enter emergency contact name"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 @error('emergency_contact_name') border-red-500 @enderror">
                            @error('emergency_contact_name')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Emergency Contact Relation -->
                        <div>
                            <label for="emergency_contact_relation" class="block text-sm font-semibold text-gray-700 mb-2">
                                Relationship
                            </label>
                            <input type="text" id="emergency_contact_relation" name="emergency_contact_relation" value="{{ old('emergency_contact_relation') }}"
                                placeholder="Enter relationship (e.g., Spouse, Parent, Sibling)"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 @error('emergency_contact_relation') border-red-500 @enderror">
                            @error('emergency_contact_relation')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Emergency Contact Phone -->
                        <div class="md:col-span-2">
                            <label for="emergency_contact_phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                Contact Phone
                            </label>
                            <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}"
                                placeholder="Enter emergency contact phone"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 @error('emergency_contact_phone') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Please use with country code. (ex. +20)</p>
                            @error('emergency_contact_phone')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Company Information Section -->
                <div class="mb-10">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 pb-3 border-b-2 border-primary/20 flex items-center">
                        <i class="fas fa-building text-primary mr-3 p-2 bg-primary/10 rounded-lg"></i>
                        Company Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Department -->
                        <div>
                            <label for="department_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                Department <span class="text-primary">*</span>
                            </label>
                            <select id="department_id" name="department_id" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 @error('department_id') border-red-500 @enderror">
                                <option value="">Select Department</option>
                                @foreach(\App\Models\Department::where('status', true)->get() as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Position -->
                        <div>
                            <label for="position_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                Position <span class="text-primary">*</span>
                            </label>
                            <select id="position_id" name="position_id" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 @error('position_id') border-red-500 @enderror">
                                <option value="">Select Position</option>
                                @foreach(\App\Models\Position::where('status', true)->get() as $position)
                                    <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                                @endforeach
                            </select>
                            @error('position_id')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Employee Level -->
                        <div>
                            <label for="level" class="block text-sm font-semibold text-gray-700 mb-2">
                                Employee Level <span class="text-primary">*</span>
                            </label>
                            <select id="level" name="level" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 @error('level') border-red-500 @enderror">
                                <option value="">Select Employee Level</option>
                                <option value="internship" {{ old('level', 'entry') === 'internship' ? 'selected' : '' }}>Internship</option>
                                <option value="entry" {{ old('level', 'entry') === 'entry' ? 'selected' : '' }}>Entry</option>
                                <option value="junior" {{ old('level') === 'junior' ? 'selected' : '' }}>Junior</option>
                                <option value="mid" {{ old('level') === 'mid' ? 'selected' : '' }}>Mid</option>
                                <option value="senior" {{ old('level') === 'senior' ? 'selected' : '' }}>Senior</option>
                                <option value="lead" {{ old('level') === 'lead' ? 'selected' : '' }}>Lead</option>
                                <option value="manager" {{ old('level') === 'manager' ? 'selected' : '' }}>Manager</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Select the employee level/seniority</p>
                            @error('level')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contract Type -->
                        <div>
                            <label for="contract_type" class="block text-sm font-semibold text-gray-700 mb-2">
                                Contract Type <span class="text-primary">*</span>
                            </label>
                            <select id="contract_type" name="contract_type" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 @error('contract_type') border-red-500 @enderror">
                                <option value="">Select Contract Type</option>
                                <option value="permanent" {{ old('contract_type', 'permanent') === 'permanent' ? 'selected' : '' }}>Permanent</option>
                                <option value="fulltime" {{ old('contract_type') === 'fulltime' ? 'selected' : '' }}>Full Time</option>
                                <option value="parttime" {{ old('contract_type') === 'parttime' ? 'selected' : '' }}>Part Time</option>
                                <option value="freelance" {{ old('contract_type') === 'freelance' ? 'selected' : '' }}>Freelance</option>
                                <option value="credit_hours" {{ old('contract_type') === 'credit_hours' ? 'selected' : '' }}>Credit Hours</option>
                                <option value="internship" {{ old('contract_type') === 'internship' ? 'selected' : '' }}>Internship</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Select the employment contract type</p>
                            @error('contract_type')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Reporting Manager -->
                        <div>
                            <label for="reporting_to" class="block text-sm font-semibold text-gray-700 mb-2">
                                Reports To (Manager)
                            </label>
                            <select id="reporting_to" name="reporting_to"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 @error('reporting_to') border-red-500 @enderror">
                                <option value="">Select Direct Manager</option>
                                @foreach(\App\Models\Employee::where('status', true)->get() as $manager)
                                    <option value="{{ $manager->id }}" {{ old('reporting_to') == $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Select the direct manager this employee reports to</p>
                            @error('reporting_to')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Company Joining Date -->
                        <div>
                            <label for="company_date_of_joining" class="block text-sm font-semibold text-gray-700 mb-2">
                                Company Date Of Joining <span class="text-primary">*</span>
                            </label>
                            <input type="date" id="company_date_of_joining" name="company_date_of_joining" value="{{ old('company_date_of_joining', today()->toDateString()) }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 @error('company_date_of_joining') border-red-500 @enderror">
                            @error('company_date_of_joining')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Password Section -->
                <div class="mb-10">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 pb-3 border-b-2 border-primary/20 flex items-center">
                        <i class="fas fa-lock text-primary mr-3 p-2 bg-primary/10 rounded-lg"></i>
                        Account Security
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                Password <span class="text-primary">*</span>
                            </label>
                            <input type="password" id="password" name="password" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 @error('password') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Minimum 8 characters with letters, numbers and symbols</p>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password Confirmation -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                                Confirm Password <span class="text-primary">*</span>
                            </label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 @error('password_confirmation') border-red-500 @enderror">
                            @error('password_confirmation')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-center pt-6">
                    <button type="submit" 
                        class="bg-gradient-to-r from-primary to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white font-bold py-4 px-12 rounded-2xl shadow-xl transition duration-300 ease-in-out transform hover:scale-105 hover:shadow-2xl focus:outline-none focus:ring-4 focus:ring-primary/25">
                        <i class="fas fa-user-plus mr-3 text-lg"></i>
                        Register as Employee
                    </button>
                </div>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mt-8 bg-red-50 border-l-4 border-red-400 rounded-xl p-6 dark:bg-red-900/20">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-red-800 mb-2">
                                    Please correct the following errors:
                                </h3>
                                <div class="text-sm text-red-700">
                                    <ul class="list-disc list-inside space-y-2">
                                        @foreach ($errors->all() as $error)
                                            <li class="font-medium">{{ $error }}</li>
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
        <div class="text-center mt-12 text-gray-500 text-sm">
            <p class="font-medium">&copy; {{ date('Y') }} {{ get_setting('company_name', 'HRM System') }}. All rights reserved.</p>
        </div>
    </div>

    <script>
        // Enhanced form validation and interactions
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input[required], select[required]');
            const departmentSelect = document.getElementById('department_id');
            const positionSelect = document.getElementById('position_id');
            
            // Form validation feedback
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.value.trim() === '') {
                        this.classList.remove('border-green-500', 'focus:border-primary');
                        this.classList.add('border-red-500');
                    } else {
                        this.classList.remove('border-red-500');
                        this.classList.add('border-green-500');
                    }
                });
                
                input.addEventListener('focus', function() {
                    this.classList.remove('border-red-500', 'border-green-500');
                    this.classList.add('focus:border-primary');
                });
            });
            
            // Department change handler for position filtering
            if (departmentSelect && positionSelect) {
                departmentSelect.addEventListener('change', function() {
                    const departmentId = this.value;
                    // You can add AJAX call here to filter positions by department
                    // For now, this is a placeholder for dynamic position loading
                });
            }
        });
    </script>
</body>
</html>