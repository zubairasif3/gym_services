<x-filament-panels::page>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Main Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700">
            
            <!-- Header with Icon -->
            <div class="px-6 sm:px-8 lg:px-10 pt-8 pb-6 text-center border-b border-gray-200 dark:border-gray-700 pt-4">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full border-2 border-white mb-4 shadow-lg">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
                
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    Preview Your Public Profile
                </h2>
                
                <p class="text-base text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    See how your profile appears to customers and other users
                </p>
            </div>
            
            <!-- Content Section -->
            <div class="px-6 sm:px-8 lg:px-10 py-8">
                @if(auth()->user()->isProfessional())
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 mb-8">
                        <a href="{{ route('professional.preview') }}" 
                           target="_blank"
                           class="flex-1 group inline-flex items-center justify-center gap-3 px-6 py-4 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-650 text-gray-700 dark:text-gray-200 font-semibold rounded-xl transition-all duration-200 shadow-md hover:shadow-lg border-2 border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span class="text-base">Profile Preview</span>
                            <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                        
                        <a href="{{ route('profile.edit') }}" 
                           class="flex-1 group inline-flex items-center justify-center gap-3 px-6 py-4 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-650 text-gray-700 dark:text-gray-200 font-semibold rounded-xl transition-all duration-200 shadow-md hover:shadow-lg border-2 border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                            <svg class="w-5 h-5 group-hover:rotate-12 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span class="text-base">Edit Profile</span>
                        </a>
                    </div>
                    
                    <!-- Info Alert -->
                    <div class="pt-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 dark:border-blue-400 rounded-lg p-5">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 mt-0.5">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-1">
                                    Professional Profile Active
                                </h3>
                                <p class="text-sm text-blue-800 dark:text-blue-300 leading-relaxed">
                                    Your profile is visible to customers. Click "Profile Preview" to see how it looks.
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Non-Professional Account -->
                    <div class="text-center mb-8">
                        <a href="{{ route('profile.edit') }}" 
                           class="inline-flex items-center justify-center gap-3 px-8 py-4 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-650 text-gray-700 dark:text-gray-200 font-semibold rounded-xl transition-all duration-200 shadow-md hover:shadow-lg border-2 border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span class="text-base">Edit Profile</span>
                        </a>
                    </div>
                    
                    <!-- Warning Alert -->
                    <div class="bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 dark:border-amber-400 rounded-lg p-5">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 mt-0.5">
                                <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-sm font-semibold text-amber-900 dark:text-amber-200 mb-1">
                                    Professional Account Required
                                </h3>
                                <p class="text-sm text-amber-800 dark:text-amber-300 leading-relaxed">
                                    Profile preview is only available for professional accounts. Upgrade your account to make your profile visible to customers.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            
        </div>
    </div>
</x-filament-panels::page>

