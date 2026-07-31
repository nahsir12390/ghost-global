@php
    $isEdit = isset($category);
    $nameValue = old('name', $category->name ?? '');
    $descriptionValue = old('description', $category->description ?? '');
    $isActiveValue = (bool) old('is_active', $category->is_active ?? true);
    $currentImage = $category->image ?? null;
@endphp

<div class="space-y-6">
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900">Category Information</h3>
            <p class="mt-1 text-sm text-gray-500">Add the category details your store uses to organize products.</p>
        </div>

        <div class="space-y-6 p-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Category Name *</label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ $nameValue }}"
                       required
                       class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20 @error('name') border-red-500 @enderror"
                       placeholder="e.g. Electronics, Fashion, Home Appliances">
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea id="description"
                          name="description"
                          rows="4"
                          class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20 @error('description') border-red-500 @enderror"
                          placeholder="Short description for this category">{{ $descriptionValue }}</textarea>
                @error('description')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-6 lg:grid-cols-[1fr_280px]">
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Category Image</label>
                    <div class="rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50/70 p-5">
                        <input type="file"
                               id="image"
                               name="image"
                               accept=".jpg,.jpeg,.png,.gif,.webp"
                               class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-xl file:border-0 file:bg-red-600 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-white hover:file:bg-red-700">
                        <p class="mt-3 text-xs text-gray-500">Accepted: JPG, PNG, GIF, WEBP up to 2MB.</p>
                        @error('image')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        @if($isEdit && $currentImage)
                            <label class="mt-4 inline-flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox"
                                       name="remove_image"
                                       value="1"
                                       {{ old('remove_image') ? 'checked' : '' }}
                                       class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                                Remove current image
                            </label>
                        @endif
                    </div>
                </div>

                <div>
                    <p class="block text-sm font-medium text-gray-700 mb-1">Current Preview</p>
                    <div class="flex h-full min-h-[220px] items-center justify-center rounded-2xl border border-gray-200 bg-white p-4">
                        @if($isEdit && $currentImage && !old('remove_image'))
                            <img src="{{ Storage::url($currentImage) }}"
                                 alt="{{ $category->name }}"
                                 class="max-h-52 w-full rounded-xl object-cover">
                        @else
                            <div class="text-center text-sm text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="mt-3">{{ $isEdit ? 'No category image saved.' : 'Upload an image to represent this category.' }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <label class="flex items-center gap-3 rounded-2xl border border-gray-200 bg-gray-50 px-4 py-4">
                <input type="checkbox"
                       id="is_active"
                       name="is_active"
                       value="1"
                       {{ $isActiveValue ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                <span>
                    <span class="block text-sm font-medium text-gray-900">Active Category</span>
                    <span class="block text-sm text-gray-500">Show this category across the storefront and admin tools.</span>
                </span>
            </label>
        </div>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
        <a href="{{ route('admin.categories.index') }}"
           class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Cancel
        </a>
        <button type="submit"
                class="inline-flex items-center justify-center rounded-xl bg-red-600 px-5 py-3 text-sm font-semibold text-white hover:bg-red-700">
            {{ $isEdit ? 'Update Category' : 'Create Category' }}
        </button>
    </div>
</div>
