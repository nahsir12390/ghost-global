<div>
    <!-- Success Message -->
    @if($success && $message)
        <div class="mb-6 p-4 bg-green-600 border border-green-500 rounded-xl text-white shadow-lg animate-fade-in">
            <div class="flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ $message }}</span>
            </div>
        </div>
    @endif
    
    <!-- Error Message -->
    @if($error)
        <div class="mb-6 p-4 bg-red-600 border border-red-500 rounded-xl text-white shadow-lg animate-fade-in">
            <div class="flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span>{{ $error }}</span>
            </div>
        </div>
    @endif
    
    <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-block px-6 py-3 bg-gradient-to-r from-red-500 to-pink-500 rounded-full mb-6 shadow-lg">
            <span class="text-white font-bold text-lg">Stay Updated</span>
        </div>
        <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
            Get 10% Off Your First Order
        </h2>
        <p class="text-xl text-gray-300 mb-10">
            Subscribe to our newsletter and be the first to know about new arrivals, exclusive offers, and style tips.
        </p>
        
        <!-- Newsletter Form -->
        <form wire:submit.prevent="subscribe" class="max-w-2xl mx-auto">
            <div class="flex flex-col sm:flex-row gap-4">
                <input type="email" 
                       wire:model.defer="newsletter_email"
                       placeholder="Enter your email address" 
                       required
                       class="flex-1 px-6 py-4 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300">
                <button type="submit" 
                        wire:loading.attr="disabled"
                        class="px-8 py-4 bg-gradient-to-r from-red-600 to-pink-600 text-white font-bold rounded-full hover:from-red-700 hover:to-pink-700 transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove>Subscribe Now</span>
                    <span wire:loading>
                        <svg class="animate-spin h-5 w-5 inline-block mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Subscribing...
                    </span>
                </button>
            </div>
            <p class="text-sm text-gray-400 mt-4">
                By subscribing, you agree to our Privacy Policy and consent to receive updates.
            </p>
        </form>
    </div>
</div>

@push('styles')
<style>
    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in {
        animation: fade-in 0.5s ease-out;
    }
</style>
@endpush
