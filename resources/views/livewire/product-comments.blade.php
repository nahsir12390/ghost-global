<div class="grid gap-8 lg:grid-cols-[.78fr_1.22fr] lg:gap-12">
    <aside class="lg:sticky lg:top-28 lg:self-start">
        <p class="text-xs font-bold uppercase tracking-[.24em] text-red-400">Customer voices</p>
        <h2 class="mt-3 text-3xl font-bold tracking-tight text-white sm:text-4xl">Reviews from real shoppers.</h2>
        <p class="mt-3 max-w-md text-sm leading-6 text-slate-400">Read genuine product experiences or help another shopper by sharing yours.</p>
        <div class="mt-7 rounded-[2rem] border border-white/10 bg-white/[.06] p-6 backdrop-blur">
            <div class="flex items-end gap-4">
                <strong class="text-6xl font-bold tracking-tighter text-white">{{ $totalComments ? number_format($averageRating, 1) : '—' }}</strong>
                <div class="pb-1">
                    <div class="flex gap-1 text-amber-400" aria-label="{{ number_format($averageRating, 1) }} out of 5 stars">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="h-5 w-5 {{ $i <= round($averageRating) ? 'fill-current' : 'fill-white/10 text-white/10' }}" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        @endfor
                    </div>
                    <p class="mt-1 text-xs text-slate-400">Based on {{ $totalComments }} {{ Str::plural('review', $totalComments) }}</p>
                </div>
            </div>
            <div class="mt-6 space-y-3 border-t border-white/10 pt-5 text-xs text-slate-300">
                <p><span class="mr-2 text-emerald-300">✓</span>Reviews are attached to this product</p>
                <p><span class="mr-2 text-sky-300">↗</span>Your feedback helps future customers</p>
            </div>
        </div>
    </aside>

    <div class="space-y-6">
        <section class="rounded-[2rem] bg-white p-5 text-slate-900 shadow-[0_28px_70px_-38px_rgba(0,0,0,.7)] sm:p-8" x-data="{ formOpen: {{ $totalComments ? 'false' : 'true' }} }">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div><p class="text-xs font-bold uppercase tracking-[.2em] text-red-600">Your experience</p><h3 class="mt-2 text-xl font-bold">{{ Auth::check() ? 'Share your review' : 'Write a review' }}</h3></div>
                <button type="button" @click="formOpen = !formOpen" :aria-expanded="formOpen" class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white transition hover:bg-red-600"><span x-text="formOpen ? 'Close form' : 'Write a review'"></span></button>
            </div>

            <form x-show="formOpen" x-transition.opacity.duration.200ms wire:submit="submitComment" class="mt-7 space-y-5" style="display: none;">
                <fieldset>
                    <legend class="text-sm font-semibold text-slate-700">How would you rate it?</legend>
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" wire:click="$set('rating', {{ $i }})" aria-label="Rate {{ $i }} out of 5" class="rounded-xl p-1.5 transition hover:-translate-y-0.5 hover:bg-amber-50 focus:outline-none focus:ring-4 focus:ring-amber-100">
                                <svg class="h-8 w-8 {{ $rating >= $i ? 'fill-amber-400 text-amber-400' : 'fill-slate-200 text-slate-200' }}" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                            </button>
                        @endfor
                        <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">{{ $rating }}/5</span>
                    </div>
                    @error('rating') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </fieldset>

                @guest
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div><label for="review-name" class="text-sm font-semibold text-slate-700">Your name</label><input id="review-name" type="text" wire:model.defer="guest_name" autocomplete="name" placeholder="Full name" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm outline-none transition focus:border-red-400 focus:bg-white focus:ring-4 focus:ring-red-100">@error('guest_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror</div>
                        <div><label for="review-email" class="text-sm font-semibold text-slate-700">Email address</label><input id="review-email" type="email" wire:model.defer="guest_email" autocomplete="email" placeholder="you@example.com" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm outline-none transition focus:border-red-400 focus:bg-white focus:ring-4 focus:ring-red-100">@error('guest_email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror</div>
                    </div>
                    @error('guest_info') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                @endguest

                <div>
                    <div class="flex justify-between gap-3"><label for="product-review" class="text-sm font-semibold text-slate-700">Your review</label><span class="text-xs text-slate-400">5–1000 characters</span></div>
                    <textarea id="product-review" wire:model.defer="comment" rows="5" maxlength="1000" placeholder="What did you like? How was the quality and delivery?" class="mt-2 w-full resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm leading-6 outline-none transition focus:border-red-400 focus:bg-white focus:ring-4 focus:ring-red-100"></textarea>
                    @error('comment') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <button type="submit" wire:loading.attr="disabled" wire:target="submitComment" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-red-600 px-5 py-3.5 text-sm font-bold text-white shadow-lg shadow-red-100 transition hover:bg-red-700 disabled:opacity-60 sm:w-auto">
                    <svg wire:loading wire:target="submitComment" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12H4z"/></svg>
                    <span wire:loading.remove wire:target="submitComment">Publish review</span><span wire:loading wire:target="submitComment">Publishing...</span>
                </button>
            </form>
        </section>

        @if($comments->isNotEmpty())
            <section aria-label="Customer review list" class="space-y-4">
                @foreach($comments as $review)
                    @php $reviewerName = $review->user?->name ?: ($review->guest_name ?: 'Verified shopper'); @endphp
                    <article class="rounded-[1.75rem] border border-white/10 bg-white/[.06] p-5 backdrop-blur transition hover:bg-white/[.09] sm:p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex min-w-0 items-center gap-3">
                                @if($review->user?->profile_photo_path)<img src="{{ asset('storage/'.$review->user->profile_photo_path) }}" alt="" class="h-11 w-11 rounded-2xl object-cover">@else<span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-red-500 to-red-700 font-bold text-white">{{ strtoupper(mb_substr($reviewerName, 0, 1)) }}</span>@endif
                                <div class="min-w-0"><h4 class="truncate font-semibold text-white">{{ $reviewerName }}</h4><p class="mt-0.5 text-xs text-slate-500">{{ $review->created_at->diffForHumans() }}</p></div>
                            </div>
                            <span class="shrink-0 rounded-full bg-amber-400/10 px-3 py-1.5 text-xs font-bold text-amber-300">★ {{ $review->rating }}.0</span>
                        </div>
                        <p class="mt-5 text-sm leading-7 text-slate-300">{{ $review->comment }}</p>
                    </article>
                @endforeach
                <div class="pt-3">{{ $comments->links() }}</div>
            </section>
        @else
            <div class="rounded-[2rem] border border-dashed border-white/15 bg-white/[.04] px-6 py-12 text-center"><div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-white/[.07] text-xl">☆</div><h3 class="mt-4 font-semibold text-white">Be the first reviewer</h3><p class="mt-2 text-sm text-slate-400">Your experience could help the next shopper decide.</p></div>
        @endif
    </div>
</div>
