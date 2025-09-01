<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ get_setting('company_name', env('APP_NAME', 'Company Portal')) }} - Your Digital Workplace</title>
    <meta name="description"
        content="Access your company's digital workplace. Connect with your team and stay productive with our integrated business platform.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Custom Animations -->
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes pulse-glow {

            0%,
            100% {
                box-shadow: 0 0 5px rgba(233, 113, 118, 0.5);
            }

            50% {
                box-shadow: 0 0 20px rgba(233, 113, 118, 0.8);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        .animate-pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }

        .stagger-1 {
            animation-delay: 0.1s;
        }

        .stagger-2 {
            animation-delay: 0.2s;
        }

        .stagger-3 {
            animation-delay: 0.3s;
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Custom gradient text animation */
        .gradient-text {
            background: linear-gradient(-45deg, #e97176, #f8a5c2, #c3a6ff, #a8edea);
            background-size: 400% 400%;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradient 4s ease infinite;
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* Primary color theme */
        .bg-primary {
            background-color: #e97176;
        }

        .text-primary {
            color: #e97176;
        }

        .border-primary {
            border-color: #e97176;
        }

        .from-primary {
            --tw-gradient-from: #e97176;
        }

        .to-primary {
            --tw-gradient-to: #e97176;
        }

        .hover\:bg-primary-dark:hover {
            background-color: #d85a60;
        }

        .shadow-primary {
            box-shadow: 0 10px 25px rgba(233, 113, 118, 0.2);
        }
    </style>

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            /* Tailwind CSS inline styles would go here */
            body {
                margin: 0;
                font-family: Inter, ui-sans-serif, system-ui, sans-serif;
            }

            .min-h-screen {
                min-height: 100vh;
            }

            .flex {
                display: flex;
            }

            .items-center {
                align-items: center;
            }

            .justify-center {
                justify-content: center;
            }

            .text-center {
                text-align: center;
            }

            .bg-gradient-to-br {
                background: linear-gradient(to bottom right, var(--tw-gradient-stops));
            }

            .from-slate-50 {
                --tw-gradient-from: rgb(248 250 252);
            }

            .via-rose-50 {
                --tw-gradient-to: rgb(255 241 242) var(--tw-gradient-to-position);
                --tw-gradient-stops: var(--tw-gradient-from), rgb(255 241 242) var(--tw-gradient-via-position), var(--tw-gradient-to);
            }

            .to-pink-50 {
                --tw-gradient-to: rgb(253 242 248);
            }

            .dark\:from-slate-900:where(.dark, .dark *) {
                --tw-gradient-from: rgb(15 23 42);
            }

            .dark\:via-slate-900:where(.dark, .dark *) {
                --tw-gradient-to: rgb(15 23 42) var(--tw-gradient-to-position);
                --tw-gradient-stops: var(--tw-gradient-from), rgb(15 23 42) var(--tw-gradient-via-position), var(--tw-gradient-to);
            }

            .dark\:to-slate-800:where(.dark, .dark *) {
                --tw-gradient-to: rgb(30 41 59);
            }

            /* Add more minimal styles as needed */
        </style>
    @endif
</head>

<body
    class="font-sans antialiased bg-gradient-to-br from-slate-50 via-rose-50 to-pink-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 min-h-screen">

    <!-- Centered Logo at Top -->
    <div class="flex justify-center pt-6 pb-2">
        @if (get_setting('logo_light') || get_setting('logo_dark'))
            <img src="{{ get_setting('logo_light') ?: get_setting('logo_dark') }}"
                alt="{{ get_setting('company_name', 'Company Portal') }} Logo"
                class="h-16 w-auto dark:hidden animate-fade-in-up">
            @if (get_setting('logo_dark'))
                <img src="{{ get_setting('logo_dark') }}" alt="{{ get_setting('company_name', 'Company Portal') }} Logo"
                    class="hidden dark:block h-16 w-auto animate-fade-in-up">
            @endif
        @else
            <div class="flex flex-col items-center animate-fade-in-up">
                <div
                    class="flex items-center justify-center w-16 h-16 bg-gradient-to-br from-primary to-primary rounded-xl shadow-primary mb-3">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                </div>
                <div class="text-center">
                    <h1
                        class="text-2xl font-bold bg-gradient-to-r from-primary to-primary bg-clip-text text-transparent">
                        {{ get_setting('company_name', env('APP_NAME', 'Company Portal')) }}
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Company Portal</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Session Messages -->
    @if (session('success') || session('error') || session('info'))
        <div class="pb-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-400 text-green-700 rounded-r-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-400 text-red-700 rounded-r-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            {{ session('error') }}
                        </div>
                    </div>
                @endif

                @if (session('info'))
                    <div class="mb-4 p-4 bg-blue-50 border-l-4 border-blue-400 text-blue-700 rounded-r-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            {{ session('info') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Hero Section -->
    <section class="relative overflow-hidden flex items-center justify-center" style="min-height: calc(100vh - 120px);">
        <!-- Background Elements -->
        <div class="absolute inset-0 opacity-20">
            <div
                class="absolute top-0 left-1/2 -translate-x-1/2 w-96 h-96 bg-gradient-to-r from-primary to-pink-400 rounded-full filter blur-3xl animate-float">
            </div>
            <div class="absolute bottom-0 right-0 w-72 h-72 bg-gradient-to-r from-purple-400 to-primary rounded-full filter blur-3xl animate-float"
                style="animation-delay: 1s;"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
            <div class="max-w-4xl mx-auto">
                <h1
                    class="text-5xl sm:text-6xl lg:text-7xl font-bold text-gray-900 dark:text-white mb-8 leading-tight animate-fade-in-up">
                    Welcome to Your
                    <span class="gradient-text block mt-2">
                        Digital Workspace
                    </span>
                </h1>
                <p
                    class="text-xl sm:text-2xl text-gray-600 dark:text-gray-300 mb-12 max-w-3xl mx-auto leading-relaxed animate-fade-in-up stagger-1">
                    Connect, collaborate, and stay productive with our integrated company portal designed for modern
                    teams.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-6 justify-center items-center animate-fade-in-up stagger-2">
                    @guest
                        <a href="/admin/login"
                            class="group inline-flex items-center px-10 py-5 bg-primary text-white font-semibold text-xl rounded-2xl hover:brightness-110 hover:scale-105 transition-all duration-300 shadow-primary hover:shadow-2xl transform w-full sm:w-auto">
                            <svg class="w-6 h-6 mr-3 group-hover:scale-110 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                </path>
                            </svg>
                            HR Portal
                        </a>
                        <a href="/employee/login"
                            class="group inline-flex items-center px-10 py-5 bg-white dark:bg-slate-800 border-2 border-primary text-primary dark:text-primary font-semibold text-xl rounded-2xl hover:bg-primary hover:scale-105 transition-all duration-300 transform w-full sm:w-auto shadow-lg">
                            <svg class="w-6 h-6 mr-3 group-hover:scale-110 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Employee Portal
                        </a>
                    @else
                        <a href="{{ url('/dashboard') }}"
                            class="group inline-flex items-center px-12 py-6 bg-gradient-to-r from-primary to-primary text-white font-semibold text-2xl rounded-2xl hover:bg-primary-dark transition-all duration-300 shadow-primary hover:shadow-2xl transform hover:-translate-y-1">
                            <svg class="w-7 h-7 mr-4 group-hover:scale-110 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                            Go to Dashboard
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </section>

    <!-- Enhanced animations -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add floating animation to background elements
            const bgElements = document.querySelectorAll('.animate-float');
            bgElements.forEach((element, index) => {
                element.style.animationDelay = `${index * 0.5}s`;
            });

            // Add pulse effect to CTA buttons on hover
            const ctaButtons = document.querySelectorAll('a[href*="login"], a[href*="dashboard"]');
            ctaButtons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.classList.add('animate-pulse-glow');
                });
                button.addEventListener('mouseleave', function() {
                    this.classList.remove('animate-pulse-glow');
                });
            });

            // Intersection Observer for scroll animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe animated elements
            const animatedElements = document.querySelectorAll('.animate-fade-in-up');
            animatedElements.forEach(element => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(30px)';
                observer.observe(element);
            });
        });
    </script>

</body>

</html>
