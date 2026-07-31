<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Unauthorized Access</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Error Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header with gradient -->
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-8 text-center">
                <div class="text-6xl font-bold text-white mb-2">403</div>
                <h1 class="text-2xl font-bold text-white">Access Denied</h1>
            </div>

            <!-- Content -->
            <div class="px-6 py-8">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        You don't have permission to access this resource. If you believe this is an error, please contact support.
                    </p>
                </div>

                <!-- Error Details -->
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <p class="text-xs font-semibold text-red-900 uppercase tracking-wide">Error Code</p>
                    <p class="text-sm text-red-700 font-medium mt-1">Unauthorized Access (403)</p>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <a href="{{ auth()->check() ? route(auth()->user()->dashboardRouteName()) : route('home') }}" 
                       class="flex items-center justify-center gap-2 w-full bg-red-600 text-white font-semibold py-3 px-4 rounded-lg hover:bg-red-700 transition duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12a9 9 0 1 1 9-9 9.75 9.75 0 0 1-6.74 9.5M9 15.5l3 3 4-4" />
                        </svg>
                        Go to Dashboard
                    </a>
                    <a href="javascript:history.back()" 
                       class="flex items-center justify-center gap-2 w-full bg-gray-200 text-gray-700 font-semibold py-3 px-4 rounded-lg hover:bg-gray-300 transition duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Go Back
                    </a>
                </div>

                <!-- Support Link -->
                <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                    <p class="text-xs text-gray-500 mb-2">Need help?</p>
                    <a href="mailto:support@example.com" 
                       class="text-red-600 hover:text-red-700 text-sm font-medium transition">
                        Contact Support
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer Message -->
        <div class="text-center mt-6">
            <p class="text-xs text-gray-500">
                This resource is restricted. <br>
                <span class="text-gray-400">Request ID: {{ uniqid() }}</span>
            </p>
        </div>
    </div>
</body>
</html>
