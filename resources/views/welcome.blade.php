<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IJRPR - International Journal of Research Publication and Reviews</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-4xl w-full">
            <!-- Logo/Header Section -->
            <div class="text-center mb-12">
                <div class="mb-6">
                    <svg class="mx-auto h-20 w-20 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h1 class="text-5xl font-bold text-gray-800 mb-4">IJRPR</h1>
                <p class="text-xl text-gray-600 mb-2">International Journal of Research Publication and Reviews</p>
                <p class="text-sm text-gray-500">Paper Submission Management System</p>
            </div>

            <!-- Main Card -->
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="md:flex">
                    <!-- Left Side - Info -->
                    <div class="md:w-1/2 bg-gradient-to-br from-blue-600 to-indigo-700 p-12 text-white">
                        <h2 class="text-3xl font-bold mb-6">Welcome!</h2>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <svg class="h-6 w-6 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <h3 class="font-semibold mb-1">Submit Your Research</h3>
                                    <p class="text-blue-100 text-sm">Easy paper submission process</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <svg class="h-6 w-6 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <div>
                                    <h3 class="font-semibold mb-1">Secure Platform</h3>
                                    <p class="text-blue-100 text-sm">Your data is safe with us</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <svg class="h-6 w-6 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <div>
                                    <h3 class="font-semibold mb-1">Fast Processing</h3>
                                    <p class="text-blue-100 text-sm">Quick review and publication</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-12 pt-8 border-t border-blue-500">
                            <p class="text-sm text-blue-100">
                                <strong>For Administrators:</strong><br>
                                Login to access the admin panel and manage submissions.
                            </p>
                        </div>
                    </div>

                    <!-- Right Side - Actions -->
                    <div class="md:w-1/2 p-12">
                        <h2 class="text-2xl font-bold text-gray-800 mb-8">Get Started</h2>
                        
                        <!-- Login Button -->
                        <a href="{{ route('login') }}" class="block w-full mb-4">
                            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 px-6 rounded-lg transition duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <div class="flex items-center justify-center">
                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>
                                    Login to Your Account
                                </div>
                            </button>
                        </a>

                        <!-- Register Button -->
                        <a href="{{ route('register') }}" class="block w-full mb-6">
                            <button class="w-full bg-white hover:bg-gray-50 text-blue-600 font-semibold py-4 px-6 rounded-lg border-2 border-blue-600 transition duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                <div class="flex items-center justify-center">
                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                    </svg>
                                    Create New Account
                                </div>
                            </button>
                        </a>

                        <div class="relative my-8">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-4 bg-white text-gray-500">New to IJRPR?</span>
                            </div>
                        </div>

                        <!-- Info Box -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h3 class="font-semibold text-blue-900 mb-2">First Time Here?</h3>
                            <p class="text-sm text-blue-700 mb-3">
                                Register as a new user to submit your research papers. After registration, you can:
                            </p>
                            <ul class="text-sm text-blue-600 space-y-1">
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Submit research papers
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Track submission status
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Download certificates
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-8 text-gray-600 text-sm">
                <p>&copy; {{ date('Y') }} IJRPR. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
