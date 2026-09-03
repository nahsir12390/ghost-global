<div class="space-y-4">
    <!-- Current Photo Display -->
    @if($previewUrl)
        <div class="flex flex-col items-center rounded-3xl border border-slate-200 bg-slate-50 p-5">
            <img src="{{ $previewUrl }}" 
                 alt="Profile Photo Preview"
                 class="h-32 w-32 rounded-[1.75rem] border-4 border-white object-cover shadow-xl">
            <p class="text-sm text-gray-600 mt-3">Current Profile Picture</p>
        </div>
    @else
        <div class="flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-slate-300 bg-slate-50 p-8 transition hover:border-red-300 hover:bg-red-50/40">
            <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="text-gray-600 text-sm">No profile picture yet</p>
        </div>
    @endif

    <!-- File Input -->
    <div>
        <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">
            Choose Photo
        </label>
        <div class="flex items-center">
            <input 
                type="file" 
                id="photo" 
                wire:model="photo"
                accept="image/*"
                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm file:mr-4 file:rounded-full file:border-0 file:bg-slate-950 file:px-4 file:py-2 file:font-semibold file:text-white focus:border-red-400 focus:bg-white focus:ring-4 focus:ring-red-100">
        </div>
        @error('photo')
            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
        @enderror
        <p class="text-xs text-gray-500 mt-2">JPG, PNG or GIF (max 10MB, min 100x100px)</p>
    </div>

    <!-- Buttons -->
    <div class="flex gap-3 pt-2">
        @if($photo)
            <button 
                type="button"
                wire:click="updatePhoto"
                wire:loading.attr="disabled"
                wire:target="updatePhoto"
                class="flex flex-1 items-center justify-center gap-2 rounded-2xl bg-red-600 px-4 py-3 font-semibold text-white transition hover:bg-red-700 disabled:opacity-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span wire:loading.remove wire:target="updatePhoto">Upload Photo</span><span wire:loading wire:target="updatePhoto">Uploading...</span>
            </button>
            <button 
                type="button"
                wire:click="$set('photo', null)"
                class="flex-1 rounded-2xl border border-slate-200 px-4 py-3 font-semibold text-slate-700 transition hover:bg-slate-50">
                Cancel
            </button>
        @else
            @if($previewUrl)
                <button 
                    type="button"
                    wire:click="deletePhoto"
                    wire:confirm="Remove your profile picture?"
                    class="flex flex-1 items-center justify-center gap-2 rounded-2xl border border-red-200 px-4 py-3 font-semibold text-red-600 transition hover:bg-red-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete Photo
                </button>
            @endif
        @endif
    </div>
</div>
