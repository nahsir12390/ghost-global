<div class="space-y-8">
    <!-- Comments Header -->
    <div class="border-t pt-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Customer Reviews</h2>
                <p class="text-gray-600">
                    @if($totalComments > 0)
                        <span class="font-semibold">{{ $totalComments }}</span> 
                        {{ $totalComments === 1 ? 'review' : 'reviews' }}
                        • Average rating: <span class="font-semibold">{{ number_format($averageRating, 1) }}/5</span>
                    @else
                        <span class="text-gray-500">No reviews yet. Be the first to review!</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Rating Stats -->
        @if($totalComments > 0)
            <div class="bg-gray-50 rounded-lg p-6 mb-8">
                <div class="flex items-center mb-4">
                    <div class="flex text-yellow-400">
                        @for($i = 0; $i < 5; $i++)
                            @if($i < round($averageRating))
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-300 fill-current" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                </svg>
                            @endif
                        @endfor
                    </div>
                    <span class="ml-3 text-gray-600">{{ number_format($averageRating, 1) }} out of 5</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Add Comment Form -->
    <div class="bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">{{ Auth::check() ? 'Share Your Review' : 'Write a Review' }}</h3>
        
        <form wire:submit="submitComment" class="space-y-6">
            <!-- Rating Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Rating</label>
                <div class="flex gap-2">
                    @for($i = 1; $i <= 5; $i++)
                        <button 
                            type="button"
                            wire:click="$set('rating', {{ $i }})"
                            class="focus:outline-none transition-all"
                            @class(['text-yellow-400' => $rating >= $i, 'text-gray-300' => $rating < $i])
                        >
                            <svg class="w-8 h-8 fill-current cursor-pointer hover:text-yellow-400" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                        </button>
                    @endfor
                    <span class="ml-3 text-gray-600">{{ $rating }}/5</span>
                </div>
                @error('rating')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Guest Info (if not authenticated) -->
            @if(!Auth::check())
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Your Name</label>
                        <input 
                            type="text" 
                            wire:model.blur="guest_name" 
                            placeholder="Enter your name"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        >
                        @error('guest_name')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Your Email</label>
                        <input 
                            type="email" 
                            wire:model.blur="guest_email" 
                            placeholder="your@email.com"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        >
                        @error('guest_email')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                @error('guest_info')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            @endif

            <!-- Comment Textarea -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Your Review</label>
                <textarea 
                    wire:model.defer="comment"
                    placeholder="Share your experience with this product..."
                    rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"
                ></textarea>
                @error('comment')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-sm mt-2">{{ strlen($comment) }}/1000 characters</p>
            </div>

            <!-- Submit Button -->
            <button 
                type="submit"
                wire:loading.attr="disabled"
                class="w-full py-3 px-4 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
            >
                <span wire:loading.remove>Post Review</span>
                <span wire:loading>
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>
        </form>
    </div>

    <!-- Comments List -->
    @if($comments->count() > 0)
        <div class="space-y-6">
            @foreach($comments as $comment)
                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-sm transition-shadow">
                    <!-- Comment Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h4 class="font-semibold text-gray-900">
                                {{ $comment->user ? $comment->user->name : $comment->guest_name }}
                            </h4>
                            <p class="text-sm text-gray-500">{{ $comment->created_at->format('M d, Y') }}</p>
                        </div>
                        <!-- Rating Stars -->
                        <div class="flex text-yellow-400">
                            @for($i = 0; $i < 5; $i++)
                                @if($i < $comment->rating)
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-300 fill-current" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                @endif
                            @endfor
                            <span class="ml-2 text-sm font-medium text-gray-600">{{ $comment->rating }}/5</span>
                        </div>
                    </div>

                    <!-- Comment Content -->
                    <p class="text-gray-700 leading-relaxed">{{ $comment->comment }}</p>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $comments->links() }}
        </div>
    @else
        <div class="text-center py-8 bg-gray-50 rounded-lg">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900">No reviews yet</h3>
            <p class="mt-1 text-gray-500">Be the first to share your thoughts about this product!</p>
        </div>
    @endif
</div>
