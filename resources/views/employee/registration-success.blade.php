<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful - {{ get_setting('company_name', 'HRM System') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center py-8">
    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-6 text-center">
                <!-- Success Icon -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                    <i class="fas fa-check text-green-600 text-2xl"></i>
                </div>

                <!-- Success Message -->
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Registration Successful!</h1>
                <p class="text-gray-600 mb-6">
                    Welcome to {{ get_setting('company_name', 'HRM System') }}! Your employee registration has been
                    completed successfully.
                </p>

                <!-- Next Steps -->
                <div class="bg-blue-50 rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">What's Next?</h3>
                    <div class="text-sm text-blue-700 space-y-2">
                        <p class="flex items-start">
                            <i class="fas fa-envelope text-blue-500 mt-1 mr-2"></i>
                            Check your email for confirmation and login details
                        </p>
                        <p class="flex items-start">
                            <i class="fas fa-user-check text-blue-500 mt-1 mr-2"></i>
                            Your account will be reviewed and activated by HR
                        </p>
                        <p class="flex items-start">
                            <i class="fas fa-clock text-blue-500 mt-1 mr-2"></i>
                            You'll receive notification once your account is ready
                        </p>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="text-md font-semibold text-gray-900 mb-2">Need Help?</h3>
                    <p class="text-sm text-gray-600">
                        If you have any questions, please contact our HR team.
                    </p>
                </div>

                <!-- Action Button -->
                <a href="/"
                    class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-lg transition duration-300 ease-in-out transform hover:scale-105">
                    <i class="fas fa-home mr-2"></i>
                    Back to Home
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-gray-500 text-sm">
            <p>&copy; {{ date('Y') }} {{ get_setting('company_name', 'HRM System') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
