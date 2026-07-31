<div class="space-y-6" x-data="{ isActive: @entangle('is_active').defer, isFeatured: @entangle('is_featured').defer }">
    <div class="rounded-[2rem] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-amber-50 p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-white px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-amber-700">
                    <span class="inline-block h-2 w-2 rounded-full bg-amber-500"></span>
                    Product Editor
                </div>
                <h2 class="mt-4 text-2xl font-semibold tracking-tight text-slate-900">Update this product without losing momentum</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                    Edit details, replace images, and update delivery settings in one place. Most fields now wait until save, so the page feels lighter while you work.
                </p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div class="rounded-2xl border border-white/70 bg-white/80 px-4 py-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Current Type</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900">{{ ucfirst($product_type) }}</p>
                </div>
                <div class="rounded-2xl border border-white/70 bg-white/80 px-4 py-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900">{{ $is_active ? 'Active and visible' : 'Draft / hidden' }}</p>
                </div>
            </div>
        </div>
    </div>

    <form wire:submit="update" class="space-y-6">
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.1fr_.9fr]">
            <div class="space-y-6">
                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="border-b border-slate-200 pb-4">
                        <h3 class="text-lg font-semibold text-slate-900">Basic Details</h3>
                        <p class="mt-1 text-sm text-slate-500">Update the customer-facing information first.</p>
                    </div>

                    <div class="mt-5 grid gap-5">
                        @if(!auth()->user()?->isVendor())
                            <div class="rounded-2xl border border-blue-100 bg-blue-50/70 p-4">
                                <label for="vendor_id" class="mb-2 block text-sm font-medium text-slate-700">Vendor Store *</label>
                                <select id="vendor_id"
                                        wire:model.defer="vendor_id"
                                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-200 @error('vendor_id') border-red-500 @enderror">
                                    <option value="">Choose the vendor this product belongs to</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}">
                                            {{ $vendor->store_name ?: $vendor->name }}{{ $vendor->vendor_is_active ? '' : ' (Inactive)' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('vendor_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-xs text-slate-500">Reassign ownership if staff are moving this product to a different vendor store.</p>
                            </div>
                        @endif

                        <div>
                            <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Product Name *</label>
                            <input type="text"
                                   id="name"
                                   wire:model.defer="name"
                                   placeholder="e.g. Premium Leather Bag"
                                   class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-200 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label for="category_id" class="mb-2 block text-sm font-medium text-slate-700">Category *</label>
                                <select id="category_id"
                                        wire:model.defer="category_id"
                                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-200 @error('category_id') border-red-500 @enderror">
                                    <option value="">Select a category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="product_type" class="mb-2 block text-sm font-medium text-slate-700">Product Type *</label>
                                <select id="product_type"
                                        wire:model.change="product_type"
                                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-200 @error('product_type') border-red-500 @enderror">
                                    <option value="physical">Physical Product</option>
                                    <option value="digital">Digital Product</option>
                                    <option value="course">Online Course</option>
                                </select>
                                @error('product_type')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="rounded-2xl bg-slate-50 px-4 py-4 text-sm text-slate-600">
                            @if($product_type === 'physical')
                                Physical products need stock quantity and customer-ready photos.
                            @elseif($product_type === 'digital')
                                Digital products should have either an uploaded file, a download link, or both.
                            @else
                                Courses should include an access URL or clear onboarding instructions.
                            @endif
                        </div>

                        <div>
                            <label for="description" class="mb-2 block text-sm font-medium text-slate-700">Description</label>
                            <textarea id="description"
                                      wire:model.defer="description"
                                      rows="6"
                                      placeholder="Refine product details, benefits, delivery notes, or course outcomes."
                                      class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-200 @error('description') border-red-500 @enderror"></textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="border-b border-slate-200 pb-4">
                        <h3 class="text-lg font-semibold text-slate-900">Photos</h3>
                        <p class="mt-1 text-sm text-slate-500">Keep your best images, remove weak ones, and add more only when needed.</p>
                    </div>

                    <div class="mt-5 space-y-5">
                        @if(count($existingImages) > 0)
                            <div>
                                <div class="mb-3 flex items-center justify-between">
                                    <p class="text-sm font-semibold text-slate-900">Current Images</p>
                                    <p class="text-xs text-slate-500">{{ count($existingImages) }} saved</p>
                                </div>
                                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                    @foreach($existingImages as $index => $image)
                                        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                                            <div class="relative h-40 bg-slate-50">
                                                <img src="{{ Storage::url($image) }}"
                                                     alt="Product image"
                                                     class="h-full w-full object-cover">
                                                <button type="button"
                                                        wire:click="removeExistingImage({{ $index }})"
                                                        class="absolute right-3 top-3 rounded-full bg-white/95 p-2 text-slate-500 shadow-sm transition hover:text-red-600">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="px-4 py-3 text-xs text-slate-500">
                                                Saved product photo
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div>
                            <label for="newImages" class="flex cursor-pointer flex-col items-center justify-center rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center transition hover:border-red-300 hover:bg-red-50/50">
                                <svg class="h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 15a4 4 0 014-4h.586a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293h1.172a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293H17a4 4 0 014 4v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 11v6m3-3h-6" />
                                </svg>
                                <span class="mt-4 text-sm font-semibold text-slate-900">Add more product photos</span>
                                <span class="mt-2 text-xs leading-5 text-slate-500">JPEG, PNG, GIF, WEBP, HEIC, or HEIF. Smaller files upload faster.</span>
                                <input id="newImages"
                                       type="file"
                                       wire:model="newImages"
                                       multiple
                                       accept="image/jpeg,image/png,image/gif,image/webp,image/heic,image/heif"
                                       class="hidden">
                            </label>

                            <div wire:loading wire:target="newImages" class="mt-4 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                                Uploading new images. Please wait and keep this page open.
                            </div>

                            @error('newImages.*')
                                <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        @if(!empty($newImages))
                            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                @foreach($newImages as $index => $image)
                                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                                        <div class="flex items-center justify-between border-b border-slate-100 px-3 py-2">
                                            <p class="truncate text-xs font-medium text-slate-600">{{ method_exists($image, 'getClientOriginalName') ? $image->getClientOriginalName() : 'Selected image' }}</p>
                                            <button type="button"
                                                    wire:click="removeNewImage({{ $index }})"
                                                    class="rounded-full p-1 text-slate-400 transition hover:bg-slate-100 hover:text-red-600">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="flex h-36 flex-col items-center justify-center gap-2 bg-slate-50 px-4 text-center">
                                            <svg class="h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <p class="text-sm font-semibold text-slate-700">Image selected</p>
                                            <p class="text-xs text-slate-500">Preview skipped for a faster form experience.</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </section>
            </div>

            <div class="space-y-6">
                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="border-b border-slate-200 pb-4">
                        <h3 class="text-lg font-semibold text-slate-900">Pricing & Inventory</h3>
                        <p class="mt-1 text-sm text-slate-500">Keep pricing clear and stock accurate.</p>
                    </div>

                    <div class="mt-5 space-y-5">
                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label for="price" class="mb-2 block text-sm font-medium text-slate-700">Regular Price *</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-sm text-slate-400">NGN</span>
                                    <input type="number"
                                           id="price"
                                           wire:model.defer="price"
                                           step="0.01"
                                           min="0"
                                           placeholder="0.00"
                                           class="w-full rounded-2xl border border-slate-200 py-3 pl-14 pr-4 text-sm shadow-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-200 @error('price') border-red-500 @enderror">
                                </div>
                                @error('price')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="compare_price" class="mb-2 block text-sm font-medium text-slate-700">Compare at Price</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-sm text-slate-400">NGN</span>
                                    <input type="number"
                                           id="compare_price"
                                           wire:model.defer="compare_price"
                                           step="0.01"
                                           min="0"
                                           placeholder="0.00"
                                           class="w-full rounded-2xl border border-slate-200 py-3 pl-14 pr-4 text-sm shadow-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-200 @error('compare_price') border-red-500 @enderror">
                                </div>
                                @error('compare_price')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label for="sku" class="mb-2 block text-sm font-medium text-slate-700">SKU</label>
                                <div class="flex gap-2">
                                    <input type="text"
                                           id="sku"
                                           wire:model.defer="sku"
                                           class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-200 @error('sku') border-red-500 @enderror">
                                    <button type="button"
                                            wire:click="regenerateSku"
                                            class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-100">
                                        Regenerate
                                    </button>
                                </div>
                                @error('sku')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="quantity" class="mb-2 block text-sm font-medium text-slate-700">
                                    Quantity {{ $product_type === 'physical' ? '*' : '(Not needed)' }}
                                </label>
                                <input type="number"
                                       id="quantity"
                                       wire:model.defer="quantity"
                                       min="0"
                                       @if($product_type !== 'physical') disabled @endif
                                       placeholder="{{ $product_type === 'physical' ? '0' : 'Only used for physical products' }}"
                                       class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-200 disabled:cursor-not-allowed disabled:bg-slate-50 @error('quantity') border-red-500 @enderror">
                                @error('quantity')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </section>

                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="border-b border-slate-200 pb-4">
                        <h3 class="text-lg font-semibold text-slate-900">Digital Delivery</h3>
                        <p class="mt-1 text-sm text-slate-500">Only update the fields that apply to this product type.</p>
                    </div>

                    <div class="mt-5 space-y-5">
                        @if($product_type === 'digital')
                            @if($product->download_file_path)
                                <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-4 text-sm text-green-800">
                                    A digital file is already attached to this product. Upload a new file only if you want to replace it.
                                </div>
                            @endif

                            <div>
                                <label for="digital_file" class="mb-2 block text-sm font-medium text-slate-700">Replace Uploaded File</label>
                                <input type="file"
                                       id="digital_file"
                                       wire:model="digital_file"
                                       class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-200 @error('digital_file') border-red-500 @enderror">
                                <div wire:loading wire:target="digital_file" class="mt-2 text-sm text-blue-700">Uploading replacement file...</div>
                                @error('digital_file')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="download_link" class="mb-2 block text-sm font-medium text-slate-700">External Download Link</label>
                                <input type="url"
                                       id="download_link"
                                       wire:model.defer="download_link"
                                       placeholder="https://example.com/download"
                                       class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-200 @error('download_link') border-red-500 @enderror">
                                @error('download_link')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @elseif($product_type === 'course')
                            <div>
                                <label for="course_access_url" class="mb-2 block text-sm font-medium text-slate-700">Course Access URL</label>
                                <input type="url"
                                       id="course_access_url"
                                       wire:model.defer="course_access_url"
                                       placeholder="https://example.com/course-login"
                                       class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-200 @error('course_access_url') border-red-500 @enderror">
                                @error('course_access_url')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <div>
                            <label for="access_instructions" class="mb-2 block text-sm font-medium text-slate-700">Access Instructions</label>
                            <textarea id="access_instructions"
                                      wire:model.defer="access_instructions"
                                      rows="5"
                                      placeholder="Add delivery notes, download instructions, course login steps, or post-purchase support details."
                                      class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-200 @error('access_instructions') border-red-500 @enderror"></textarea>
                            @error('access_instructions')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="border-b border-slate-200 pb-4">
                        <h3 class="text-lg font-semibold text-slate-900">Publishing</h3>
                        <p class="mt-1 text-sm text-slate-500">Control visibility and merchandising from here.</p>
                    </div>

                    <div class="mt-5 space-y-4">
                        <div class="flex w-full items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-left">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">Active Product</p>
                                <p class="mt-1 text-xs text-slate-500">Visible to customers when enabled.</p>
                            </div>
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="checkbox" x-model="isActive" class="peer sr-only">
                                <span class="h-6 w-11 rounded-full bg-slate-300 transition peer-checked:bg-red-600"></span>
                                <span class="absolute left-0 top-0 inline-block h-5 w-5 translate-x-0.5 translate-y-0.5 rounded-full bg-white shadow transition peer-checked:translate-x-5"></span>
                            </label>
                        </div>

                        <div class="flex w-full items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-left">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">Featured Product</p>
                                <p class="mt-1 text-xs text-slate-500">Show this product more prominently across the store.</p>
                            </div>
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="checkbox" x-model="isFeatured" class="peer sr-only">
                                <span class="h-6 w-11 rounded-full bg-slate-300 transition peer-checked:bg-amber-500"></span>
                                <span class="absolute left-0 top-0 inline-block h-5 w-5 translate-x-0.5 translate-y-0.5 rounded-full bg-white shadow transition peer-checked:translate-x-5"></span>
                            </label>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="sticky bottom-4 z-10">
            <div class="flex flex-col gap-3 rounded-3xl border border-slate-200 bg-white/95 p-4 shadow-lg backdrop-blur sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-900">Ready to save your changes?</p>
                    <p class="text-xs text-slate-500">The product will keep its current image set plus any new images you add here.</p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('admin.products.index') }}"
                       class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                        Cancel
                    </a>
                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="update,newImages,digital_file"
                            class="inline-flex items-center justify-center rounded-2xl bg-red-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <span wire:loading.remove wire:target="update,newImages,digital_file">Save Changes</span>
                        <span wire:loading wire:target="update,newImages,digital_file">Saving...</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
