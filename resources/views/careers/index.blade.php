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
    <title>Jobs – {{ get_setting('company_name') }}</title>
</head>

<body class="antialiased font-sans text-gray-700 bg-gray-100">

    <!-- Navigation Bar -->
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 bg-transparent transition-all duration-300">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Contact Button & Mobile Menu -->
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

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }

        // Filter form functionality
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('job-filter-form');
            const clearButton = document.getElementById('clear-filters');
            const searchInput = document.getElementById('search');

            // Check if there are any filter parameters and scroll to filter section
            const urlParams = new URLSearchParams(window.location.search);
            const hasFilters = urlParams.has('search') ||
                urlParams.has('experience_level') ||
                urlParams.has('work_mode') ||
                urlParams.has('work_type');

            if (hasFilters) {
                // Scroll to filter section with smooth animation
                setTimeout(() => {
                    const filterSection = document.getElementById('job-filter-form');
                    if (filterSection) {
                        filterSection.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }, 100); // Small delay to ensure page is fully loaded
            }

            // Auto-submit on filter change
            const filterSelects = form.querySelectorAll('select');
            filterSelects.forEach(select => {
                select.addEventListener('change', function() {
                    form.submit();
                });
            });

            // Search input with debounce
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    form.submit();
                }, 500);
            });

            // Clear filters
            clearButton.addEventListener('click', function() {
                // Clear all form inputs
                searchInput.value = '';
                filterSelects.forEach(select => {
                    select.selectedIndex = 0;
                });

                // Submit form to show all jobs
                form.submit();
            });

            // Function to clear all filters (for placeholder button)
            window.clearAllFilters = function() {
                const form = document.getElementById('job-filter-form');
                const searchInput = document.getElementById('search');
                const filterSelects = form.querySelectorAll('select');

                // Clear all form inputs
                searchInput.value = '';
                filterSelects.forEach(select => {
                    select.selectedIndex = 0;
                });

                // Submit form to show all jobs
                form.submit();
            };
        });

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

        <div class="mx-auto max-w-7xl px-6 pt-16 lg:px-8 lg:pt-20 relative z-10">
            <!-- Page heading -->
            <div class="flex flex-col items-center text-center">
                <!-- Logo with enhanced styling -->
                <div class="relative mb-8 group">
                    <div
                        class="absolute -inset-1 bg-white/20 rounded-2xl blur-sm group-hover:blur-md transition-all duration-300">
                    </div>
                    <a href="#"
                        class="relative block bg-white/10 backdrop-blur-sm rounded-2xl p-4 border border-white/20 hover:bg-white/20 transition-all duration-300">
                        <img src="{{ get_setting('logo_light', '/images/logos/logo-light.svg') }}"
                            alt="{{ get_setting('company_name') }} logo" class="h-16 w-auto">
                    </a>
                </div>

                <!-- Enhanced heading with gradient text -->
                <div class="relative">
                    <h1 class="text-2xl lg:text-5xl xl:text-6xl font-bold text-white mb-4 tracking-tight">
                        Careers at
                    </h1>
                    <h2
                        class="text-3xl lg:text-5xl xl:text-6xl font-bold bg-gradient-to-r from-white via-white to-yellow-200 bg-clip-text text-transparent mb-6">
                        {{ get_setting('company_name') }}
                    </h2>
                </div>

                <!-- Subtitle with enhanced styling -->
                <p class="max-w-2xl text-lg lg:text-xl text-white/90 leading-relaxed mb-8 font-medium">
                    Join our dynamic team and build the future of technology.
                    Discover exciting opportunities that match your passion and expertise.
                </p>

                <!-- Stats or features section -->
                <div class="hidden md:flex flex-wrap justify-center gap-8 mb-12 text-white/80">
                    <div
                        class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full px-6 py-3 border border-white/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <span class="font-semibold">50+ Open Positions</span>
                    </div>
                    <div
                        class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full px-6 py-3 border border-white/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        <span class="font-semibold">Remote Friendly</span>
                    </div>
                    <div
                        class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full px-6 py-3 border border-white/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <span class="font-semibold">Fast Growing</span>
                    </div>
                </div>
            </div>

            <!-- Search card -->
            <div class="relative z-10 mt-16 mb-[-5rem] rounded-xl bg-white px-8 py-10 lg:px-12">
                <form id="job-filter-form" method="GET" action="{{ route('jobs.index') }}">
                    <!-- Search bar in separate row -->
                    <div class="mb-6">
                        <label for="search" class="sr-only">Search for jobs</label>
                        <div class="relative">
                            <input id="search" name="search" type="text" placeholder="Search for jobs.."
                                value="{{ request('search') }}"
                                class="w-full rounded border border-gray-300 py-3 pl-4 pr-12 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500" />
                            <svg class="absolute right-4 top-3.5 h-5 w-5 text-gray-400" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-4.35-4.35M16 10a6 6 0 11-12 0 6 6 0 0112 0z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Filters and submit button -->
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-end">
                        <!-- Filters -->
                        <div class="flex flex-col gap-6 sm:flex-row sm:gap-4 lg:flex-grow">
                            <div class="flex-grow">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Experience Level</label>
                                <div class="relative">
                                    <select name="experience_level" id="experience_level"
                                        class="w-full rounded-lg border-2 border-gray-200 py-3 pl-4 pr-10 text-gray-700 bg-white appearance-none company-focus company-ring transition-all duration-200 cursor-pointer hover:border-gray-300">
                                        <option value="">Select an Option</option>
                                        <option value="entry"
                                            {{ request('experience_level') == 'entry' ? 'selected' : '' }}>Entry
                                        </option>
                                        <option value="mid"
                                            {{ request('experience_level') == 'mid' ? 'selected' : '' }}>Mid</option>
                                        <option value="senior"
                                            {{ request('experience_level') == 'senior' ? 'selected' : '' }}>Senior
                                        </option>
                                        <option value="lead"
                                            {{ request('experience_level') == 'lead' ? 'selected' : '' }}>Lead</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="flex-grow">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Work Mode</label>
                                <div class="relative">
                                    <select name="work_mode" id="work_mode"
                                        class="w-full rounded-lg border-2 border-gray-200 py-3 pl-4 pr-10 text-gray-700 bg-white appearance-none company-focus company-ring transition-all duration-200 cursor-pointer hover:border-gray-300">
                                        <option value="">All Work Modes</option>
                                        <option value="remote"
                                            {{ request('work_mode') == 'remote' ? 'selected' : '' }}>Remote</option>
                                        <option value="onsite"
                                            {{ request('work_mode') == 'onsite' ? 'selected' : '' }}>On-site</option>
                                        <option value="hybrid"
                                            {{ request('work_mode') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="flex-grow">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Work Type</label>
                                <div class="relative">
                                    <select name="work_type" id="work_type"
                                        class="w-full rounded-lg border-2 border-gray-200 py-3 pl-4 pr-10 text-gray-700 bg-white appearance-none company-focus company-ring transition-all duration-200 cursor-pointer hover:border-gray-300">
                                        <option value="">All Types</option>
                                        <option value="full-time"
                                            {{ request('work_type') == 'full-time' ? 'selected' : '' }}>Full-time
                                        </option>
                                        <option value="part-time"
                                            {{ request('work_type') == 'part-time' ? 'selected' : '' }}>Part-time
                                        </option>
                                        <option value="contract"
                                            {{ request('work_type') == 'contract' ? 'selected' : '' }}>Contract
                                        </option>
                                        <option value="internship"
                                            {{ request('work_type') == 'internship' ? 'selected' : '' }}>Internship
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" id="clear-filters"
                            class="inline-flex items-center justify-center px-8 py-3 company-bg text-white rounded-lg company-bg-hover transition-all duration-200 font-semibold text-sm shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </svg>
                            Clear
                        </button>
                        <!-- Submit and Clear buttons -->
                        {{-- <div class="flex gap-3 mt-6 lg:mt-0">
                            <button type="button" id="clear-filters"
                                class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 bg-white rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 font-medium text-sm">
                                <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </svg>
                                Clear
                            </button>
                            <button type="submit"
                                class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r bg-[#16c47f] text-white rounded-lg hover:from-[#14b574] hover:to-[#12a569] transition-all duration-200 font-semibold text-sm shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Search Jobs
                            </button>
                        </div> --}}
                    </div>
            </div>

        </div>

        <!-- creates the curved bottom cut-out -->
        <svg class="absolute bottom-0 w-full text-white" viewBox="0 0 1440 80" fill="none"
            style="margin-bottom: -1px;" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 80s320-80 720-80 720 80 720 80H0z" fill="#F3F4F6" />
        </svg>
    </section>

    <!-- ▾ Rest of page content spacer -->
    <main class="min-h-[100px] pt-36">
        <!-- Your job list goes here -->
    </main>

    <section class="mx-auto max-w-7xl px-4 mb-5 lg:px-6">

        <!-- ① Layout wrapper -->
        <div class="grid gap-8 lg:grid-cols-1">

            <!-- ② Jobs list -->
            <div class="space-y-4">

                @forelse ($jobs as $job)
                    <!-- -- Enhanced job card -- -->
                    <div class="group block rounded-2xl border border-gray-200 bg-white p-6 shadow-sm hover:shadow-lg transition-all duration-300 hover:border-opacity-30"
                        style="--tw-border-opacity: 0.3; border-color: {{ get_setting('primary_color') }}30;">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            <!-- Left content -->
                            <div class="flex-1">
                                <div class="flex items-start justify-between mb-3">
                                    <h3
                                        class="text-xl font-bold text-gray-900 group-hover:company-text transition-colors duration-200">
                                        {{ $job->title }}
                                    </h3>
                                </div>

                                <!-- Company info -->
                                <div class="flex items-center gap-4 mb-3 text-sm text-gray-600">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                            </path>
                                        </svg>
                                        {{ get_setting('company_name') }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>
                                        </svg>
                                        {{ $job->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                <!-- tag chips -->
                                <div class="flex flex-wrap gap-2 mb-4">
                                    <!-- Experience level -->
                                    @php
                                        $levelColors = [
                                            'entry' => 'bg-blue-50 text-blue-700 border border-blue-200',
                                            'mid' => 'bg-green-50 text-green-700 border border-green-200',
                                            'senior' => 'bg-purple-50 text-purple-700 border border-purple-200',
                                            'lead' => 'bg-orange-50 text-orange-700 border border-orange-200',
                                        ];

                                        $levelNames = [
                                            'entry' => 'Entry Level',
                                            'mid' => 'Mid Level',
                                            'senior' => 'Senior Level',
                                            'lead' => 'Lead/Principal',
                                        ];

                                        $colorClasses =
                                            $levelColors[$job->experience_level] ??
                                            'bg-gray-50 text-gray-700 border border-gray-200';
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $colorClasses }}">
                                        {{ $levelNames[$job->experience_level] ?? $job->experience_level }}
                                    </span>
                                    @php
                                        $workTypeColors = [
                                            'full-time' => 'bg-purple-50 text-purple-700 border border-purple-200',
                                            'part-time' => 'bg-blue-50 text-blue-700 border border-blue-200',
                                            'contract' => 'bg-amber-50 text-amber-700 border border-amber-200',
                                            'internship' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                        ];

                                        $workTypeNames = [
                                            'full_time' => 'Full-time',
                                            'part_time' => 'Part-time',
                                            'contract' => 'Contract',
                                            'internship' => 'Internship',
                                        ];

                                        $workTypeColorClasses =
                                            $workTypeColors[$job->work_type] ??
                                            'bg-gray-50 text-gray-700 border border-gray-200';
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $workTypeColorClasses }}">
                                        {{ $workTypeNames[$job->work_type] ?? $job->work_type }}
                                    </span>
                                    @php
                                        $workModeColors = [
                                            'remote' => 'bg-orange-50 text-orange-700 border border-orange-200',
                                            'onsite' => 'bg-teal-50 text-teal-700 border border-teal-200',
                                            'hybrid' => 'bg-indigo-50 text-indigo-700 border border-indigo-200',
                                        ];

                                        $workModeNames = [
                                            'remote' => 'Remote',
                                            'onsite' => 'On-site',
                                            'hybrid' => 'Hybrid',
                                        ];

                                        $workModeColorClasses =
                                            $workModeColors[$job->work_mode] ??
                                            'bg-gray-50 text-gray-700 border border-gray-200';
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $workModeColorClasses }}">
                                        {{ $workModeNames[$job->work_mode] ?? $job->work_mode }}
                                    </span>
                                </div>

                                <!-- teaser paragraph -->
                                <p class="text-sm text-gray-600 leading-relaxed line-clamp-2">
                                    {{ $job->short_description }}
                                </p>
                            </div>

                            <!-- Right content -->
                            <div class="flex flex-col lg:items-end gap-3 lg:min-w-[200px]">
                                <a href="{{ route('jobs.show', $job->slug) }}"
                                    class="inline-flex items-center justify-center px-6 py-2.5 company-border company-text bg-white rounded-lg hover:company-bg transition-all duration-200 font-medium text-sm group-hover:shadow-md">
                                    View Details
                                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- No jobs placeholder -->
                    <div class="rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 p-12 text-center">
                        <div class="mx-auto max-w-md">
                            <!-- Icon -->
                            <div
                                class="mx-auto mb-6 w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                @php
                                    // Check if any search or filter parameters are present
                                    $hasSearch = request()->filled('search');
                                    $hasFilters = request()->filled([
                                        'experience_level',
                                        'branch_id',
                                        'work_mode',
                                        'work_type',
                                    ]);
                                    $hasAnyFilters = $hasSearch || $hasFilters;
                                @endphp

                                @if ($hasAnyFilters)
                                    <!-- Search icon for filtered results -->
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                @else
                                    <!-- Briefcase icon for no jobs available -->
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6">
                                        </path>
                                    </svg>
                                @endif
                            </div>

                            <!-- Title and description -->
                            @if ($hasAnyFilters)
                                <h3 class="text-xl font-semibold text-gray-900 mb-3">No matching jobs found</h3>
                                <p class="text-gray-600 mb-6 leading-relaxed">
                                    @if ($hasSearch && $hasFilters)
                                        We couldn't find any jobs matching "<strong>{{ request('search') }}</strong>"
                                        with your selected filters. Try adjusting your search terms or filters to see
                                        more results.
                                    @elseif ($hasSearch)
                                        We couldn't find any jobs matching "<strong>{{ request('search') }}</strong>".
                                        Try different keywords or browse all available positions.
                                    @else
                                        We couldn't find any jobs that match your selected filters. Try adjusting your
                                        criteria to see more results.
                                    @endif
                                </p>

                                <!-- Clear filters button -->
                                <button onclick="clearAllFilters()"
                                    class="inline-flex items-center justify-center px-6 py-3 company-bg text-white rounded-lg company-bg-hover transition-all duration-200 font-medium text-sm shadow-md hover:shadow-lg">
                                    <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                        </path>
                                    </svg>
                                    @if ($hasSearch && $hasFilters)
                                        Clear Search & Filters
                                    @elseif ($hasSearch)
                                        Clear Search
                                    @else
                                        Clear All Filters
                                    @endif
                                </button>
                            @else
                                <h3 class="text-xl font-semibold text-gray-900 mb-3">No open positions available</h3>
                                <p class="text-gray-600 mb-6 leading-relaxed">
                                    We don't have any job openings at
                                    <strong>{{ get_setting('company_name') }}</strong> right now.
                                    But don't worry! New opportunities are posted regularly. Check back soon or get in
                                    touch with us to learn about upcoming positions.
                                </p>

                                <!-- Contact us button -->
                                <a href="/contact"
                                    class="inline-flex items-center justify-center px-6 py-3 company-bg text-white rounded-lg company-bg-hover transition-all duration-200 font-medium text-sm shadow-md hover:shadow-lg">
                                    <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                        </path>
                                    </svg>
                                    Get Notified About New Jobs
                                </a>
                            @endif
                        </div>
                    </div>
                @endforelse

            </div>

        </div>
    </section>

    <!-- Enhanced Banner Section -->
    <section class="relative bg-gradient-to-r from-gray-50 to-gray-100 py-16 overflow-hidden">
        <!-- Background decorative elements -->
        <div class="absolute inset-0">
            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br opacity-5"
                style="background: linear-gradient(to bottom right, {{ get_setting('primary_color') }}, transparent);">
            </div>
            <div class="absolute -top-20 -right-20 w-80 h-80 rounded-full blur-3xl opacity-10"
                style="background-color: {{ get_setting('primary_color') }};"></div>
            <div class="absolute -bottom-10 -left-10 w-60 h-60 rounded-full blur-2xl opacity-5"
                style="background-color: {{ get_setting('primary_color') }};"></div>
        </div>

        <div class="relative mx-auto max-w-6xl px-6 lg:px-8">
            <div class="relative rounded-3xl overflow-hidden company-bg p-8 lg:p-16">

                <!-- Decorative background pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-0 left-0 w-full h-full bg-[url('data:image/svg+xml,%3Csvg width="40"
                        height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"%3E%3Cg
                        fill="%23ffffff" fill-opacity="0.1"%3E%3Ccircle cx="20" cy="20" r="1"
                        /%3E%3C/g%3E%3C/svg%3E")]"></div>
                </div>

                <!-- Enhanced decorative elements -->
                <div
                    class="absolute -top-10 -left-10 w-32 h-32 bg-white/10 rounded-full backdrop-blur-sm border border-white/20">
                </div>
                <div class="absolute top-8 right-8 w-16 h-16 bg-white/5 rounded-full"></div>
                <div class="absolute bottom-8 left-8 w-20 h-20 bg-white/5 rounded-full blur-sm"></div>

                <!-- Modern geometric shapes -->
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
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>

                    <!-- Enhanced heading -->
                    <h2 class="text-4xl lg:text-5xl xl:text-6xl text-white font-bold mb-6 tracking-tight">
                        Let's Work
                        <span
                            class="block bg-gradient-to-r from-yellow-200 to-yellow-100 bg-clip-text text-transparent">
                            Together
                        </span>
                    </h2>

                    <!-- Enhanced description -->
                    <p class="mx-auto max-w-3xl mb-10 text-lg lg:text-xl text-white/90 leading-relaxed font-medium">
                        Whether you're a fresher or an experienced professional, explore
                        <span class="font-semibold text-yellow-200">{{ get_setting('company_name') }}</span> careers
                        for
                        opportunities to grow, learn, and lead in the tech industry.
                    </p>

                    <!-- Enhanced CTA buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <a href="#contact"
                            class="inline-flex items-center justify-center px-8 py-4 bg-white company-text rounded-xl font-bold text-lg shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-200 group border-2 border-transparent hover:border-white/20">
                            <svg class="mr-2 w-5 h-5 group-hover:rotate-12 transition-transform duration-200"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                </path>
                            </svg>
                            Ask Any Questions
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

</body>

</html>
