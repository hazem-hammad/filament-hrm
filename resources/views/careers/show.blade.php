<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @vite('resources/css/app.css')
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --company-color: {{ get_setting('primary_color') }};
            --company-color-hover: {{ get_setting('primary_color') }}dd;
            --company-color-light: {{ get_setting('primary_color') }}1a;
        }

        .company-bg {
            background-color: var(--company-color);
        }

        .company-bg-hover:hover {
            background-color: var(--company-color-hover);
        }

        .company-text {
            color: var(--company-color);
        }

        .company-border {
            border-color: var(--company-color);
        }

        .company-ring {
            --tw-ring-color: var(--company-color-light);
        }

        .company-focus:focus {
            border-color: var(--company-color);
        }
    </style>
    <title>{{ $job->title }} – {{ $companyName }}</title>

    <!-- reCAPTCHA Scripts -->
    @if (config('recaptcha.enabled'))
        @if (config('recaptcha.version') === 'v2')
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        @else
            <script src="https://www.google.com/recaptcha/api.js?render={{ config('recaptcha.site_key') }}"></script>
        @endif
    @endif
</head>

<body class="antialiased font-sans text-gray-700 bg-gray-100">

    <!-- Navigation Bar -->
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 bg-transparent transition-all duration-300">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Left side: Back to Jobs -->
                <div class="flex items-center space-x-4">
                    <!-- Back to Jobs Button -->
                    <a href="{{ route('jobs.index', ['companyName' => request()->route('companyName')]) }}"
                        class="inline-flex items-center justify-center px-3 py-2 lg:px-4 lg:py-2 bg-white/10 backdrop-blur-sm text-white rounded-lg hover:bg-white/20 transition-all duration-200 font-medium text-sm border border-white/20 hover:border-white/30 group">
                        <svg class="mr-1.5 lg:mr-2 w-4 h-4 group-hover:-translate-x-1 transition-transform duration-200"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18">
                            </path>
                        </svg>
                        <span class="hidden sm:inline">Back to Jobs</span>
                        <span class="sm:hidden">Back</span>
                    </a>
                </div>

                <!-- Right side: Contact Button -->
                <div class="flex items-center space-x-4">
                    <!-- Contact Button -->
                    <a href="/contact"
                        class="inline-flex items-center justify-center px-4 py-2 company-bg text-white rounded-lg company-bg-hover transition-all duration-200 font-medium text-sm shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                            </path>
                        </svg>
                        Contact Us
                    </a>
                </div>
            </div>

        </div>
    </nav>

    <!-- ▾ Hero section with curved bottom edge -->
    <section class="relative overflow-visible company-bg pt-16">

        <!-- decorative big half-circle -->
        <div
            class="absolute right-0 top-10 hidden md:block h-[600px] w-[900px] bg-white/10 -translate-y-1/2 rounded-[50%] backdrop-blur-sm">
        </div>

        <!-- Additional decorative elements -->
        <div class="absolute top-20 left-10 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
        <div class="absolute top-40 right-20 w-24 h-24 bg-white/5 rounded-full blur-xl"></div>
        <div class="absolute top-60 left-1/4 w-16 h-16 bg-white/5 rounded-full blur-lg"></div>

        <!-- Animated background pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-full h-full bg-[url('data:image/svg+xml,%3Csvg width="60" height="60"
                viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg
                fill="%23ffffff" fill-opacity="0.1"%3E%3Ccircle cx="30" cy="30" r="1"
                /%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] animate-pulse"></div>
        </div>

        <div class="mx-auto max-w-5xl px-4 pt-12 lg:px-8 lg:pt-20 relative z-10">
            <!-- Page heading -->
            <div class="flex flex-col items-center text-center">

                <!-- Job Title and Company -->
                <div class="relative">
                    <h1
                        class="text-2xl sm:text-2xl lg:text-4xl xl:text-4xl font-bold text-white mb-3 lg:mb-4 tracking-tight">
                        {{ $job->title }}
                    </h1>
                </div>

                <!-- Job meta information -->
                <div class="flex flex-wrap justify-center gap-2 sm:gap-3 lg:gap-4 mb-8 lg:mb-12 text-white/90">
                    <div
                        class="flex items-center gap-1.5 sm:gap-2 bg-white/10 backdrop-blur-sm rounded-full px-3 py-2 sm:px-4 sm:py-2.5 lg:px-6 lg:py-3 border border-white/20">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span
                            class="font-semibold text-xs sm:text-sm lg:text-base">{{ \Illuminate\Support\Str::title(str_replace(['_'], ' ', $job->work_type)) }}</span>
                    </div>
                    <div
                        class="flex items-center gap-1.5 sm:gap-2 bg-white/10 backdrop-blur-sm rounded-full px-3 py-2 sm:px-4 sm:py-2.5 lg:px-6 lg:py-3 border border-white/20">
                        @if (strtolower($job->work_mode) === 'remote')
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg>
                        @elseif(strtolower($job->work_mode) === 'hybrid')
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        @else
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        @endif
                        <span
                            class="font-semibold text-xs sm:text-sm lg:text-base">{{ \Illuminate\Support\Str::title(str_replace(['_'], ' ', $job->work_mode)) }}</span>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <!-- ▾ Job Details Content -->
    <main class="min-h-[100px] pt-16">
        <div class="mx-auto max-w-6xl px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-2">

                <!-- Left Column: Job Details -->
                <div class="space-y-8">
                    <!-- Job Description -->
                    <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 company-text mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Job Description
                        </h3>
                        <div class="prose prose-gray max-w-none">
                            <div class="text-gray-600 leading-relaxed">
                                {!! $job->long_description ??
                                    ($job->short_description ??
                                        'This is an exciting opportunity to join our team and contribute to innovative projects.') !!}
                            </div>
                        </div>
                    </div>

                    <!-- Requirements -->
                    <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 company-text mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                </path>
                            </svg>
                            Requirements
                        </h3>
                        <div class="prose prose-gray max-w-none">
                            <div class="text-gray-600 leading-relaxed">
                                {!! $job->job_requirements ??
                                    '<ul class="list-disc ml-5"><li>Bachelor\'s degree or equivalent experience</li><li>Strong communication skills</li><li>Problem-solving ability</li><li>Team collaboration</li></ul>' !!}
                            </div>
                        </div>
                    </div>

                    <!-- Benefits -->
                    <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 company-text mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                </path>
                            </svg>
                            What We Offer
                        </h3>
                        <div class="prose prose-gray max-w-none">
                            <div class="text-gray-600 leading-relaxed">
                                {!! $job->benefits ??
                                    '<ul class="grid grid-cols-1 md:grid-cols-2 gap-2 list-disc ml-5"><li>Competitive salary</li><li>Health insurance</li><li>Flexible working hours</li><li>Professional development</li><li>Team events</li><li>Modern equipment</li></ul>' !!}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Application Form -->
                <div class="lg:sticky lg:top-8 h-fit">
                    <div class="bg-white rounded-2xl p-8 shadow-xl border border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 company-text mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Apply Now
                        </h3>

                        <form id="jobApplicationForm"
                            action="{{ route('job.apply', ['companyName' => request()->route('companyName'), 'slug' => request()->route('slug')]) }}"
                            method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf

                            <!-- Application Status Messages -->
                            <div id="applicationMessages" class="hidden">
                                <div id="successMessage"
                                    class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 hidden">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <span id="successText">Application submitted successfully!</span>
                                    </div>
                                </div>
                                <div id="errorMessage"
                                    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 hidden">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <span id="errorText">Please correct the errors below.</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Name Fields -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First
                                        Name *</label>
                                    <input type="text" id="first_name" name="first_name" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg company-focus company-ring transition-all duration-200">
                                </div>
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last
                                        Name *</label>
                                    <input type="text" id="last_name" name="last_name" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg company-focus company-ring transition-all duration-200">
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email
                                    Address *</label>
                                <input type="email" id="email" name="email" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg company-focus company-ring transition-all duration-200">
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone
                                    Number *</label>
                                <input type="tel" id="phone" name="phone" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg company-focus company-ring transition-all duration-200">
                            </div>

                            <!-- Experience -->
                            <div>
                                <label for="years_of_experience"
                                    class="block text-sm font-medium text-gray-700 mb-2">Years of Experience *</label>
                                <select id="years_of_experience" name="years_of_experience" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg company-focus company-ring transition-all duration-200">
                                    <option value="">Select your experience years</option>
                                    <option value="0">
                                        < 1 year</option>
                                    <option value="1">1 year</option>
                                    <option value="2">2 years</option>
                                    <option value="3">3 years</option>
                                    <option value="4">4 years</option>
                                    <option value="5">5 years</option>
                                    <option value="6">6+ years</option>
                                </select>
                            </div>

                            <!-- Resume Upload -->
                            <div>
                                <label for="resume" class="block text-sm font-medium text-gray-700 mb-2">Resume/CV
                                    *</label>
                                <div class="relative">
                                    <input type="file" id="resume" name="resume"
                                        accept=".pdf,.doc,.docx,.xlsx,.xls,.jpg,.jpeg,.png,.gif,.txt,.csv" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg company-focus company-ring transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:company-bg file:text-white file:font-medium file:hover:company-bg-hover">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">PDF, DOC, DOCX, XLSX, XLS, JPG, PNG, TXT, CSV
                                    (max 5MB)</p>
                            </div>

                            <!-- LinkedIn -->
                            <div>
                                <label for="linkedin_url"
                                    class="block text-sm font-medium text-gray-700 mb-2">LinkedIn
                                    Profile</label>
                                <input type="url" id="linkedin_url" name="linkedin_url"
                                    placeholder="https://linkedin.com/in/yourprofile"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg company-focus company-ring transition-all duration-200">
                            </div>

                            <!-- Portfolio -->
                            <div>
                                <label for="portfolio_url"
                                    class="block text-sm font-medium text-gray-700 mb-2">Portfolio URL</label>
                                <input type="url" id="portfolio_url" name="portfolio_url"
                                    placeholder="https://yourportfolio.com"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg company-focus company-ring transition-all duration-200">
                            </div>

                            <!-- GitHub -->
                            <div>
                                <label for="github_url" class="block text-sm font-medium text-gray-700 mb-2">GitHub
                                    Profile</label>
                                <input type="url" id="github_url" name="github_url"
                                    placeholder="https://github.com/yourusername"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg company-focus company-ring transition-all duration-200">
                            </div>

                            <!-- Custom Questions -->
                            @if ($job->customQuestions && $job->customQuestions->count() > 0)
                                <div class="border-t border-gray-200 pt-6 mt-6">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Additional Questions</h4>
                                    @foreach ($job->customQuestions as $question)
                                        <div class="mb-6">
                                            <label for="custom_question_{{ $question->id }}"
                                                class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ $question->title }}
                                                @if ($question->is_required)
                                                    <span class="text-red-500">*</span>
                                                @endif
                                            </label>

                                            @switch($question->type)
                                                @case('text_field')
                                                    <input type="text" id="custom_question_{{ $question->id }}"
                                                        name="custom_questions[{{ $question->id }}]"
                                                        @if ($question->is_required) required @endif
                                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg company-focus company-ring transition-all duration-200">
                                                @break

                                                @case('textarea')
                                                    <textarea id="custom_question_{{ $question->id }}" name="custom_questions[{{ $question->id }}]" rows="4"
                                                        @if ($question->is_required) required @endif
                                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg company-focus company-ring transition-all duration-200"></textarea>
                                                @break

                                                @case('date')
                                                    <input type="date" id="custom_question_{{ $question->id }}"
                                                        name="custom_questions[{{ $question->id }}]"
                                                        @if ($question->is_required) required @endif
                                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg company-focus company-ring transition-all duration-200">
                                                @break

                                                @case('file_upload')
                                                    <input type="file" id="custom_question_{{ $question->id }}"
                                                        name="custom_questions[{{ $question->id }}]"
                                                        @if ($question->is_required) required @endif
                                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg company-focus company-ring transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:company-bg file:text-white file:font-medium file:hover:company-bg-hover">
                                                @break

                                                @case('toggle')
                                                    <div class="flex items-center">
                                                        <input type="hidden" name="custom_questions[{{ $question->id }}]"
                                                            value="0">
                                                        <input type="checkbox" id="custom_question_{{ $question->id }}"
                                                            name="custom_questions[{{ $question->id }}]" value="1"
                                                            @if ($question->is_required) required @endif
                                                            class="h-4 w-4 company-text focus:company-ring border-gray-300 rounded">
                                                        <label for="custom_question_{{ $question->id }}"
                                                            class="ml-2 block text-sm text-gray-700">Yes</label>
                                                    </div>
                                                @break

                                                @case('multi_select')
                                                    @if ($question->options && is_array($question->options))
                                                        <div class="space-y-2">
                                                            @foreach ($question->options as $option)
                                                                <div class="flex items-center">
                                                                    <input type="checkbox"
                                                                        id="custom_question_{{ $question->id }}_{{ $loop->index }}"
                                                                        name="custom_questions[{{ $question->id }}][]"
                                                                        value="{{ $option }}"
                                                                        class="h-4 w-4 company-text focus:company-ring border-gray-300 rounded">
                                                                    <label
                                                                        for="custom_question_{{ $question->id }}_{{ $loop->index }}"
                                                                        class="ml-2 block text-sm text-gray-700">{{ $option }}</label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                @break
                                            @endswitch
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Honeypot field (hidden from users, should remain empty) -->
                            <input type="text" name="website" style="display: none;" tabindex="-1"
                                autocomplete="off">

                            <!-- reCAPTCHA -->
                            @if (config('recaptcha.enabled'))
                                <div class="recaptcha-container">
                                    @if (config('recaptcha.version') === 'v2')
                                        <div class="g-recaptcha" data-sitekey="{{ config('recaptcha.site_key') }}"
                                            data-theme="light" data-size="normal">
                                        </div>
                                    @else
                                        <!-- reCAPTCHA v3 will be handled by JavaScript -->
                                        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                                    @endif

                                    <!-- reCAPTCHA Error Display -->
                                    <div id="recaptcha-error"
                                        class="hidden mt-2 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            <span id="recaptcha-error-text">Please complete the reCAPTCHA
                                                verification.</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Submit Button -->
                            <button type="submit" id="submitBtn"
                                class="w-full company-bg text-white font-bold py-4 px-6 rounded-lg company-bg-hover transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center group disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                                <svg id="submitIcon"
                                    class="w-5 h-5 mr-2 group-hover:rotate-12 transition-transform duration-200"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                <svg id="loadingIcon" class="w-5 h-5 mr-2 animate-spin hidden" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span id="submitText">Submit Application</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Enhanced Application Success Banner -->
    <section class="relative bg-gradient-to-r from-gray-50 to-gray-100 py-16 overflow-hidden mt-16">
        <!-- Background decorative elements -->
        <div class="absolute inset-0">
            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br opacity-5"
                style="background: linear-gradient(to bottom right, {{ $companyColor }}, transparent);"></div>
            <div class="absolute -top-20 -right-20 w-80 h-80 rounded-full blur-3xl opacity-10"
                style="background-color: {{ $companyColor }};"></div>
            <div class="absolute -bottom-10 -left-10 w-60 h-60 rounded-full blur-2xl opacity-5"
                style="background-color: {{ $companyColor }};"></div>
        </div>

        <div class="relative mx-auto max-w-6xl px-6 lg:px-8">
            <div class="relative rounded-3xl overflow-hidden company-bg p-8 lg:p-16">

                <!-- Decorative background pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-0 left-0 w-full h-full bg-[url('data:image/svg+xml,%3Csvg width="40"
                        height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"%3E%3Cg
                        fill="%23ffffff" fill-opacity="0.1"%3E%3Ccircle cx="20" cy="20" r="1"
                        /%3E%3C/g%3E%3C/svg%3E")]">
                    </div>
                </div>

                <!-- Enhanced decorative elements -->
                <div
                    class="absolute -top-10 -left-10 w-32 h-32 bg-white/10 rounded-full backdrop-blur-sm border border-white/20">
                </div>
                <div class="absolute top-8 right-8 w-16 h-16 bg-white/5 rounded-full"></div>
                <div class="absolute bottom-8 left-8 w-20 h-20 bg-white/5 rounded-full blur-sm"></div>
                <div
                    class="absolute top-0 right-0 w-24 h-24 transform rotate-45 bg-gradient-to-br from-white/10 to-transparent rounded-lg">
                </div>
                <div class="absolute bottom-0 left-1/4 w-16 h-16 transform -rotate-12 bg-white/5 rounded-lg"></div>

                <!-- Content -->
                <div class="relative z-10 text-center">
                    <!-- Icon -->
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl mb-8 border border-white/30">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                            </path>
                        </svg>
                    </div>

                    <!-- Enhanced heading -->
                    <h2 class="text-4xl lg:text-5xl xl:text-6xl text-white font-bold mb-6 tracking-tight">
                        Questions?
                        <span
                            class="block bg-gradient-to-r from-yellow-200 to-yellow-100 bg-clip-text text-transparent">
                            We're Here to Help
                        </span>
                    </h2>

                    <!-- Enhanced description -->
                    <p class="mx-auto max-w-3xl mb-10 text-lg lg:text-xl text-white/90 leading-relaxed font-medium">
                        Need more information about this role or our application process?
                        <span class="font-semibold text-yellow-200">Contact our HR team</span> – we're excited to hear
                        from you!
                    </p>

                    <!-- Enhanced CTA buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <a href="mailto:hr@{{ strtolower(str_replace(' ', '', $companyName)) }}.com"
                            class="inline-flex items-center justify-center px-8 py-4 bg-white company-text rounded-xl font-bold text-lg shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-200 group border-2 border-transparent hover:border-white/20">

                            Contact HR Team
                        </a>

                        <a href="{{ route('jobs.index') }}"
                            class="inline-flex items-center justify-center px-8 py-4 bg-white/10 backdrop-blur-sm text-white rounded-xl font-semibold text-lg border-2 border-white/30 hover:bg-white/20 hover:border-white/50 transition-all duration-200 group">
                            <svg class="mr-2 w-5 h-5 group-hover:translate-x-1 transition-transform duration-200"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18">
                                </path>
                            </svg>
                            Back to All Jobs
                        </a>
                    </div>
                </div>

                <!-- Enhanced bottom stripes -->
                <div
                    class="absolute -bottom-1 -right-1 h-32 w-[600px] bg-[length:20px_100%] bg-repeat-x bg-[linear-gradient(135deg,transparent_0_85%,white/10_85%_95%,transparent_95%_100%)] opacity-30">
                </div>
            </div>
        </div>
    </section>

    <script>
        // reCAPTCHA v3 initialization
        @if (config('recaptcha.enabled') && config('recaptcha.version') === 'v3')
            let recaptchaReady = false;
            grecaptcha.ready(function() {
                recaptchaReady = true;
            });
        @endif

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('jobApplicationForm');
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitIcon = document.getElementById('submitIcon');
            const loadingIcon = document.getElementById('loadingIcon');
            const messagesContainer = document.getElementById('applicationMessages');
            const successMessage = document.getElementById('successMessage');
            const errorMessage = document.getElementById('errorMessage');
            const successText = document.getElementById('successText');
            const errorText = document.getElementById('errorText');

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Clear previous messages
                hideMessages();

                @if (config('recaptcha.enabled'))
                    @if (config('recaptcha.version') === 'v2')
                        // Check reCAPTCHA v2
                        const recaptchaResponse = grecaptcha.getResponse();
                        if (!recaptchaResponse) {
                            showRecaptchaError('Please complete the reCAPTCHA verification.');
                            return;
                        }
                    @elseif (config('recaptcha.version') === 'v3')
                        // Handle reCAPTCHA v3
                        if (!recaptchaReady) {
                            showRecaptchaError('reCAPTCHA is still loading. Please try again.');
                            return;
                        }

                        grecaptcha.execute('{{ config('recaptcha.site_key') }}', {
                            action: '{{ config('recaptcha.action') }}'
                        }).then(function(token) {
                            document.getElementById('g-recaptcha-response').value = token;
                            submitFormWithData();
                        });
                        return; // Wait for reCAPTCHA v3 callback
                    @endif
                @endif

                submitFormWithData();
            });

            function submitFormWithData() {
                // Show loading state
                setLoadingState(true);

                // Create FormData object
                const formData = new FormData(form);

                // Submit form via AJAX
                fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        setLoadingState(false);

                        if (data.success) {
                            showSuccessMessage(data.message);
                            form.reset(); // Reset form on success

                            // Scroll to success message
                            messagesContainer.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        } else {
                            if (data.errors) {
                                // Handle validation errors
                                const errorMessages = Object.values(data.errors).flat();
                                showErrorMessage(errorMessages.join('<br>'));
                            } else {
                                showErrorMessage(data.message ||
                                    'Something went wrong. Please try again.');
                            }
                        }
                    })
                    .catch(error => {
                        setLoadingState(false);
                        console.error('Error:', error);
                        showErrorMessage('Network error. Please check your connection and try again.');
                    });
            }

            function setLoadingState(loading) {
            if (loading) {
                submitBtn.disabled = true;
                submitText.textContent = 'Submitting...';
                submitIcon.classList.add('hidden');
                loadingIcon.classList.remove('hidden');
            } else {
                submitBtn.disabled = false;
                submitText.textContent = 'Submit Application';
                submitIcon.classList.remove('hidden');
                loadingIcon.classList.add('hidden');
            }
        }

        function showSuccessMessage(message) {
            messagesContainer.classList.remove('hidden');
            successMessage.classList.remove('hidden');
            errorMessage.classList.add('hidden');
            successText.innerHTML = message;
        }

        function showErrorMessage(message) {
            messagesContainer.classList.remove('hidden');
            errorMessage.classList.remove('hidden');
            successMessage.classList.add('hidden');
            errorText.innerHTML = message;

            // Scroll to error message with a slight delay to ensure the element is visible
            setTimeout(() => {
                messagesContainer.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }, 100);
        }

        function hideMessages() {
            messagesContainer.classList.add('hidden');
            successMessage.classList.add('hidden');
            errorMessage.classList.add('hidden');

            // Hide reCAPTCHA error
            const recaptchaError = document.getElementById('recaptcha-error');
            if (recaptchaError) {
                recaptchaError.classList.add('hidden');
            }
        }

        function showRecaptchaError(message) {
            const recaptchaError = document.getElementById('recaptcha-error');
            const recaptchaErrorText = document.getElementById('recaptcha-error-text');

            if (recaptchaError && recaptchaErrorText) {
                recaptchaErrorText.textContent = message;
                recaptchaError.classList.remove('hidden');

                // Scroll to error
                recaptchaError.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        }

            // File input validation
            const resumeInput = document.getElementById('resume');
            resumeInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const maxSize = 5 * 1024 * 1024; // 5MB
                    const allowedTypes = [
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // Excel .xlsx
                        'application/vnd.ms-excel', // Excel .xls
                        'image/jpeg',
                        'image/jpg',
                        'image/png',
                        'image/gif',
                        'text/plain',
                        'text/csv'
                    ];

                    if (file.size > maxSize) {
                        alert('File size must be less than 5MB');
                        this.value = '';
                        return;
                    }

                    if (!allowedTypes.includes(file.type)) {
                        alert('Only PDF, DOC, DOCX, XLSX, XLS, JPG, PNG, GIF, TXT, and CSV files are allowed');
                        this.value = '';
                        return;
                    }
                }
            });
        });
    </script>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            const companyName = document.getElementById('company-name');
            const mobileMenuButton = document.getElementById('mobile-menu-button');

            // Desktop navigation links
            const navLinks = [
                document.getElementById('nav-link-home'),
                document.getElementById('nav-link-about'),
                document.getElementById('nav-link-services'),
                document.getElementById('nav-link-blog')
            ];

            // Mobile navigation links
            const mobileNavLinks = [
                document.getElementById('mobile-nav-home'),
                document.getElementById('mobile-nav-about'),
                document.getElementById('mobile-nav-services'),
                document.getElementById('mobile-nav-blog')
            ];

            if (window.scrollY > 50) {
                // Scrolled - white background
                navbar.classList.remove('bg-transparent');
                navbar.classList.add('bg-white/95', 'backdrop-blur-md', 'border-b', 'border-gray-200/50',
                    'shadow-sm');

                // Change company name to dark
                companyName.classList.remove('text-white', 'group-hover:text-white/80');
                companyName.classList.add('text-gray-900', 'group-hover:company-text');

                // Change mobile menu button to dark
                if (mobileMenuButton) {
                    mobileMenuButton.classList.remove('text-white/90', 'hover:text-white', 'hover:bg-white/10',
                        'focus:ring-white/50');
                    mobileMenuButton.classList.add('text-gray-700', 'hover:company-text', 'hover:bg-gray-100',
                        'focus:ring-company-color');
                }

                // Change desktop nav links to dark
                navLinks.forEach(link => {
                    if (link) {
                        link.classList.remove('text-white/90', 'hover:text-white');
                        link.classList.add('text-gray-700', 'hover:company-text');
                    }
                });

                // Change mobile nav links
                mobileNavLinks.forEach(link => {
                    if (link) {
                        link.classList.remove('text-white/90', 'hover:text-white', 'hover:bg-white/10');
                        link.classList.add('text-gray-700', 'hover:company-text', 'hover:bg-gray-50');
                    }
                });

                // Update mobile menu background
                const mobileMenu = document.getElementById('mobile-menu');
                if (mobileMenu) {
                    const mobileMenuContent = mobileMenu.querySelector('div');
                    if (mobileMenuContent) {
                        mobileMenuContent.classList.remove('bg-white/10', 'border-white/20');
                        mobileMenuContent.classList.add('bg-white', 'border-gray-200');
                    }
                }

            } else {
                // At top - transparent
                navbar.classList.remove('bg-white/95', 'backdrop-blur-md', 'border-b', 'border-gray-200/50',
                    'shadow-sm');
                navbar.classList.add('bg-transparent');

                // Change company name to white
                companyName.classList.remove('text-gray-900', 'group-hover:company-text');
                companyName.classList.add('text-white', 'group-hover:text-white/80');

                // Change mobile menu button to white
                if (mobileMenuButton) {
                    mobileMenuButton.classList.remove('text-gray-700', 'hover:company-text', 'hover:bg-gray-100',
                        'focus:ring-company-color');
                    mobileMenuButton.classList.add('text-white/90', 'hover:text-white', 'hover:bg-white/10',
                        'focus:ring-white/50');
                }

                // Change desktop nav links to white
                navLinks.forEach(link => {
                    if (link) {
                        link.classList.remove('text-gray-700', 'hover:company-text');
                        link.classList.add('text-white/90', 'hover:text-white');
                    }
                });

                // Change mobile nav links
                mobileNavLinks.forEach(link => {
                    if (link) {
                        link.classList.remove('text-gray-700', 'hover:company-text', 'hover:bg-gray-50');
                        link.classList.add('text-white/90', 'hover:text-white', 'hover:bg-white/10');
                    }
                });

                // Update mobile menu background
                const mobileMenu = document.getElementById('mobile-menu');
                if (mobileMenu) {
                    const mobileMenuContent = mobileMenu.querySelector('div');
                    if (mobileMenuContent) {
                        mobileMenuContent.classList.remove('bg-white', 'border-gray-200');
                        mobileMenuContent.classList.add('bg-white/10', 'border-white/20');
                    }
                }
            }
        });
    </script>

</body>

</html>
