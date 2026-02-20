<div class="space-y-6">
    {{-- Section Header --}}
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Customer Reviews</h2>

    <div class="grid md:grid-cols-3 gap-6">
        {{-- Rating Summary (Left Side) --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <div class="text-center mb-6">
                {{-- Big Average Rating --}}
                <div class="text-5xl font-bold text-gray-900 dark:text-white mb-2">
                    {{ number_format($this->averageRating, 1) }}
                </div>

                {{-- Visual 5 Stars --}}
                <div class="flex justify-center gap-1 mb-2">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= round($this->averageRating))
                            <svg class="w-6 h-6 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @else
                            <svg class="w-6 h-6 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endif
                    @endfor
                </div>

                {{-- Total Reviews Count --}}
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Based on {{ $this->reviewCount }} {{ Str::plural('review', $this->reviewCount) }}
                </p>
            </div>

            {{-- Rating Breakdown Bars --}}
            <div class="space-y-2">
                @foreach ($this->ratingBreakdown as $stars => $data)
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400 w-8">{{ $stars }}</span>
                        <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-amber-400 h-2 rounded-full transition-all duration-300" style="width: {{ $data['percent'] }}%"></div>
                        </div>
                        <span class="text-sm text-gray-500 dark:text-gray-400 w-10 text-right">{{ $data['count'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Reviews List (Right Side) --}}
        <div class="md:col-span-2 space-y-4">
            {{-- Write Review Button / Auth Prompt --}}
            <div class="flex items-center justify-between">
                @auth
                    @if (!$this->hasReviewed)
                        <button
                            wire:click="$set('showForm', true)"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary-dark transition"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Write a Review
                        </button>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">You have already reviewed this product.</p>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="text-primary hover:text-primary-dark font-medium">
                        Log in to write a review
                    </a>
                @endauth
                <div></div>
            </div>

            {{-- Pending Submission Message --}}
            <div wire:show="submitted" class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-green-700 dark:text-green-400">Thank you! Your review is pending approval.</p>
                </div>
            </div>

            {{-- Review Form (Collapsible) --}}
            <div wire:show="showForm" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Write Your Review</h3>

                <form wire:submit="submitReview" class="space-y-4">
                    {{-- Star Rating Selector --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Your Rating</label>
                        <div class="flex gap-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <button
                                    type="button"
                                    wire:click="$set('rating', {{ $i }})"
                                    class="focus:outline-none transition-transform hover:scale-110"
                                >
                                    @if ($i <= $rating)
                                        <svg class="w-8 h-8 text-amber-400 cursor-pointer" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @else
                                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600 cursor-pointer hover:text-amber-300" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endif
                                </button>
                            @endfor
                        </div>
                        @error('rating') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Review Title --}}
                    <div>
                        <label for="reviewTitle" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Review Title</label>
                        <input
                            type="text"
                            id="reviewTitle"
                            wire:model="reviewTitle"
                            placeholder="Summarize your experience"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-gray-700 dark:text-white"
                        >
                        @error('reviewTitle') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Review Content --}}
                    <div>
                        <label for="reviewContent" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Your Review</label>
                        <textarea
                            id="reviewContent"
                            wire:model="reviewContent"
                            rows="4"
                            placeholder="What did you like or dislike about this product?"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-gray-700 dark:text-white resize-none"
                        ></textarea>
                        @error('reviewContent') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Form Actions --}}
                    <div class="flex items-center gap-3">
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary-dark transition disabled:opacity-50"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove wire:target="submitReview">Submit Review</span>
                            <span wire:loading wire:target="submitReview" class="flex items-center gap-2">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Submitting...
                            </span>
                        </button>
                        <button
                            type="button"
                            wire:click="$set('showForm', false)"
                            class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            {{-- Reviews List --}}
            @if ($this->reviews->count() > 0)
                <div class="space-y-4">
                    @foreach ($this->reviews as $review)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    {{-- Reviewer Name --}}
                                    <p class="font-medium text-gray-900 dark:text-white">
                                        {{ $review->user?->name ?? 'Anonymous' }}
                                    </p>
                                    {{-- Date --}}
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $review->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    {{-- Verified Purchase Badge --}}
                                    @if ($review->is_verified_purchase)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-medium rounded-full">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            Verified Purchase
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Star Rating --}}
                            <div class="flex gap-0.5 mb-3">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $review->rating)
                                        <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endif
                                @endfor
                            </div>

                            {{-- Review Title (Bold) --}}
                            @if ($review->title)
                                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $review->title }}</h4>
                            @endif

                            {{-- Review Content --}}
                            <p class="text-gray-600 dark:text-gray-300 leading-relaxed">{{ $review->content }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $this->reviews->links() }}
                </div>
            @else
                {{-- No Reviews State --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 text-center">
                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 mb-2">No reviews yet</p>
                    <p class="text-sm text-gray-400 dark:text-gray-500">Be the first to share your experience with this product.</p>
                </div>
            @endif
        </div>
    </div>
</div>
