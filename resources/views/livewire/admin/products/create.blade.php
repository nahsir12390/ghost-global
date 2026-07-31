<div class="space-y-6" x-data="{ isActive: @entangle('is_active').defer, isFeatured: @entangle('is_featured').defer }">
    <div class="rounded-[2rem] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-red-50 p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-red-200 bg-white px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-red-700">
                    <span class="inline-block h-2 w-2 rounded-full bg-red-500"></span>
                    Fast Product Setup
                </div>
                <h2 class="mt-4 text-2xl font-semibold tracking-tight text-slate-900">Create a product with fewer delays</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                    This form now waits until submit for most fields, which makes product creation feel much faster. Image uploads are also more forgiving for iPhone photos.
                </p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div class="rounded-2xl border border-white/70 bg-white/80 px-4 py-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Best for Speed</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900">Fill details first, upload photos last</p>
                </div>
                <div class="rounded-2xl border border-white/70 bg-white/80 px-4 py-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">iPhone Tip</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900">HEIC photos now have a fallback upload path</p>
                </div>
            </div>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.1fr_.9fr]">
            <div class="space-y-6">
                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="border-b border-slate-200 pb-4">
                        <h3 class="text-lg font-semibold text-slate-900">Basic Details</h3>
                        <p class="mt-1 text-sm text-slate-500">Start with the information customers see first.</p>
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
                                <p class="mt-2 text-xs text-slate-500">Staff and admins can publish directly into a vendor's store from here.</p>
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
                                Physical products need stock quantity and can be delivered normally.
                            @elseif($product_type === 'digital')
                                Digital products need either a file upload, a download link, or both.
                            @else
                                Courses need an access URL or clear instructions for learners.
                            @endif
                        </div>

                        <div>
                            <label for="description" class="mb-2 block text-sm font-medium text-slate-700">Description</label>
                            <textarea id="description"
                                      wire:model.defer="description"
                                      rows="5"
                                      placeholder="Describe the product, its features, quality, delivery details, or what makes it valuable."
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
                        <p class="mt-1 text-sm text-slate-500">Upload clear product images. This part can take longer, especially with large phone photos.</p>
                    </div>

                    <div class="mt-5 space-y-4">
                        <label for="images" class="flex cursor-pointer flex-col items-center justify-center rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center transition hover:border-red-300 hover:bg-red-50/50">
                            <svg class="h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 15a4 4 0 014-4h.586a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293h1.172a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293H17a4 4 0 014 4v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 11v6m3-3h-6" />
                            </svg>
                            <span class="mt-4 text-sm font-semibold text-slate-900">Tap to choose product photos</span>
                            <span class="mt-2 text-xs leading-5 text-slate-500">JPEG, PNG, GIF, WEBP, HEIC, or HEIF. Clear photos under 2MB upload fastest.</span>
                            <input id="images"
                                   type="file"
                                   wire:model="images"
                                   multiple
                                   accept="image/jpeg,image/png,image/gif,image/webp,image/heic,image/heif"
                                   class="hidden">
                        </label>

                        <div wire:loading wire:target="images" class="rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                            Uploading images. Please wait and keep this page open.
                        </div>

                        @error('images.*')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        @if(!empty($images))
                            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                @foreach($images as $index => $image)
                                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                                        <div class="flex items-center justify-between border-b border-slate-100 px-3 py-2">
                                            <p class="truncate text-xs font-medium text-slate-600">{{ method_exists($image, 'getClientOriginalName') ? $image->getClientOriginalName() : 'Selected image' }}</p>
                                            <button type="button"
                                                    wire:click="removeImage({{ $index }})"
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

                        <div class="rounded-2xl bg-amber-50 px-4 py-4 text-sm text-amber-900">
                            <p class="font-semibold">For iPhone users</p>
                            <p class="mt-1 leading-6">If a photo is very large or saved as HEIC, upload may still take longer. For the fastest experience, open `Settings > Camera > Formats` and choose `Most Compatible`.</p>
                        </div>
                    </div>
                </section>
            </div>

            <div class="space-y-6">
                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="border-b border-slate-200 pb-4">
                        <h3 class="text-lg font-semibold text-slate-900">Pricing & Inventory</h3>
                        <p class="mt-1 text-sm text-slate-500">Set selling price, reference price, stock, and SKU.</p>
                    </div>

                    <div class="mt-5 space-y-5">
                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label for="price" class="mb-2 block text-sm font-medium text-slate-700">Regular Price *</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-sm text-slate-400">₦</span>
                                    <input type="number"
                                           id="price"
                                           wire:model.defer="price"
                                           step="0.01"
                                           min="0"
                                           placeholder="0.00"
                                           class="w-full rounded-2xl border border-slate-200 py-3 pl-10 pr-4 text-sm shadow-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-200 @error('price') border-red-500 @enderror">
                                </div>
                                @error('price')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="compare_price" class="mb-2 block text-sm font-medium text-slate-700">Compare at Price</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-sm text-slate-400">₦</span>
                                    <input type="number"
                                           id="compare_price"
                                           wire:model.defer="compare_price"
                                           step="0.01"
                                           min="0"
                                           placeholder="0.00"
                                           class="w-full rounded-2xl border border-slate-200 py-3 pl-10 pr-4 text-sm shadow-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-200 @error('compare_price') border-red-500 @enderror">
                                </div>
                                @error('compare_price')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label for="sku" class="mb-2 block text-sm font-medium text-slate-700">SKU</label>
                                <input type="text"
                                       id="sku"
                                       wire:model.defer="sku"
                                       placeholder="Leave blank to auto-generate"
                                       class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-200 @error('sku') border-red-500 @enderror">
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
                        <p class="mt-1 text-sm text-slate-500">Only fill what applies to your selected product type.</p>
                    </div>

                    <div class="mt-5 space-y-5">
                        @if($product_type === 'digital')
                            <div>
                                <label for="digital_file" class="mb-2 block text-sm font-medium text-slate-700">Upload Digital File</label>
                                <input type="file"
                                       id="digital_file"
                                       wire:model="digital_file"
                                       class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-200 @error('digital_file') border-red-500 @enderror">
                                <div wire:loading wire:target="digital_file" class="mt-2 text-sm text-blue-700">Uploading digital file...</div>
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
                                <p class="mt-2 text-xs text-slate-500">You can use a file upload, a secure link, or both.</p>
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
                                      placeholder="Add delivery notes, course login steps, redemption instructions, WhatsApp handoff notes, or anything the buyer needs after purchase."
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
                        <p class="mt-1 text-sm text-slate-500">Control visibility and whether the product is featured.</p>
                    </div>

                    <div class="mt-5 space-y-4">
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">Active Product</p>
                                <p class="mt-1 text-xs text-slate-500">Visible to customers when the product is complete.</p>
                            </div>
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="checkbox" x-model="isActive" class="peer sr-only">
                                <span class="h-6 w-11 rounded-full bg-slate-300 transition peer-checked:bg-red-600"></span>
                                <span class="absolute left-0 top-0 inline-block h-5 w-5 translate-x-0.5 translate-y-0.5 rounded-full bg-white shadow transition peer-checked:translate-x-5"></span>
                            </label>
                        </div>

                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">Featured Product</p>
                                <p class="mt-1 text-xs text-slate-500">Highlight this product across the storefront.</p>
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

        <div class="sticky bottom-4 z-10 rounded-3xl border border-slate-200 bg-white/95 p-4 shadow-xl shadow-slate-900/5 backdrop-blur">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-900">Ready to save?</p>
                    <p class="text-xs text-slate-500">The form now keeps most typing local until submit, which reduces the lag from Livewire.</p>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('admin.products.index') }}"
                       class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Cancel
                    </a>
                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="save,images,digital_file"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-red-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <span wire:loading.remove wire:target="save,images,digital_file">Create Product</span>
                        <span wire:loading wire:target="save">
                            <svg class="h-4 w-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Saving...
                        </span>
                        <span wire:loading wire:target="images,digital_file">
                            Processing upload...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
