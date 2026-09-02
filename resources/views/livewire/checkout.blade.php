<div>
    <!-- Checkout Progress -->
    <div class="mb-8">
        <div class="flex items-center justify-center space-x-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-900">Cart</div>
                    <div class="text-sm text-gray-500">Review your items</div>
                </div>
            </div>
            
            <div class="flex-1 h-0.5 bg-gray-300"></div>
            
            <div class="flex items-center">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center">
                    <span class="text-sm font-medium">2</span>
                </div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-900">Information</div>
                    <div class="text-sm text-gray-500">Shipping & Billing</div>
                </div>
            </div>
            
            <div class="flex-1 h-0.5 bg-gray-300"></div>
            
            <div class="flex items-center">
                <div class="flex-shrink-0 w-8 h-8 rounded-full border-2 border-gray-300 text-gray-400 flex items-center justify-center">
                    <span class="text-sm font-medium">3</span>
                </div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-500">Payment</div>
                    <div class="text-sm text-gray-400">Complete order</div>
                </div>
            </div>
        </div>
    </div>

   

    @if($checkoutError)
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 shadow-sm">
            <div class="flex">
                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-semibold text-red-800">Checkout Error</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>{{ $checkoutError }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Left Column - Order Information -->
        <div>
            <!-- Shipping Information -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-200">
                    {{ $requiresShipping ? 'Shipping Information' : 'Contact Information' }}
                </h2>

                @if(!$requiresShipping)
                    <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                        This order contains only digital products or courses, so delivery details are not required.
                    </div>
                @endif
                
                <div class="space-y-4">
                    <!-- Name -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="shipping_first_name" class="block text-sm font-medium text-gray-700 mb-1">
                                First Name *
                            </label>
                            <input type="text" 
                                   id="shipping_first_name"
                                   wire:model.defer="shipping_first_name"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 @error('shipping_first_name') border-red-500 @enderror">
                            @error('shipping_first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="shipping_last_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Last Name *
                            </label>
                            <input type="text" 
                                   id="shipping_last_name"
                                   wire:model.defer="shipping_last_name"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 @error('shipping_last_name') border-red-500 @enderror">
                            @error('shipping_last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Email & Phone -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="shipping_email" class="block text-sm font-medium text-gray-700 mb-1">
                                Email Address *
                            </label>
                            <input type="email" 
                                   id="shipping_email"
                                   wire:model.defer="shipping_email"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 @error('shipping_email') border-red-500 @enderror">
                            @error('shipping_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="shipping_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                Phone Number *
                            </label>
                            <input type="tel" 
                                   id="shipping_phone"
                                   wire:model.defer="shipping_phone"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 @error('shipping_phone') border-red-500 @enderror">
                            @error('shipping_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    @if($requiresShipping)
                        <div>
                            <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-1">
                                Street Address *
                            </label>
                            <input type="text" 
                                   id="shipping_address"
                                   wire:model.defer="shipping_address"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 @error('shipping_address') border-red-500 @enderror"
                                   placeholder="House number, street name">
                            @error('shipping_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="shipping_city" class="block text-sm font-medium text-gray-700 mb-1">
                                    City *
                                </label>
                                <input type="text" 
                                       id="shipping_city"
                                       wire:model.defer="shipping_city"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 @error('shipping_city') border-red-500 @enderror">
                                @error('shipping_city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="shipping_state" class="block text-sm font-medium text-gray-700 mb-1">
                                    State *
                                </label>
                                <input type="text" 
                                       id="shipping_state"
                                       wire:model.defer="shipping_state"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 @error('shipping_state') border-red-500 @enderror">
                                @error('shipping_state')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="shipping_country" class="block text-sm font-medium text-gray-700 mb-1">
                                    Country *
                                </label>
                                <input type="text" 
                                       id="shipping_country"
                                       wire:model.defer="shipping_country"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 @error('shipping_country') border-red-500 @enderror">
                                @error('shipping_country')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="shipping_postal_code" class="block text-sm font-medium text-gray-700 mb-1">
                                    Postal Code *
                                </label>
                                <input type="text" 
                                       id="shipping_postal_code"
                                       wire:model.defer="shipping_postal_code"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 @error('shipping_postal_code') border-red-500 @enderror">
                                @error('shipping_postal_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Billing Information -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">
                        Billing Information
                    </h2>
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="same_as_shipping"
                               wire:model.change="same_as_shipping"
                               class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                        <label for="same_as_shipping" class="ml-2 text-sm text-gray-700">
                            Same as shipping address
                        </label>
                    </div>
                </div>
                
                @if(!$same_as_shipping)
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="billing_first_name" class="block text-sm font-medium text-gray-700 mb-1">
                                First Name *
                            </label>
                            <input type="text" 
                                   id="billing_first_name"
                                   wire:model.defer="billing_first_name"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 @error('billing_first_name') border-red-500 @enderror">
                            @error('billing_first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="billing_last_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Last Name *
                            </label>
                            <input type="text" 
                                   id="billing_last_name"
                                   wire:model.defer="billing_last_name"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 @error('billing_last_name') border-red-500 @enderror">
                            @error('billing_last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="billing_email" class="block text-sm font-medium text-gray-700 mb-1">
                                Email Address *
                            </label>
                            <input type="email" 
                                   id="billing_email"
                                   wire:model.defer="billing_email"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 @error('billing_email') border-red-500 @enderror">
                            @error('billing_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="billing_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                Phone Number *
                            </label>
                            <input type="tel" 
                                   id="billing_phone"
                                   wire:model.defer="billing_phone"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 @error('billing_phone') border-red-500 @enderror">
                            @error('billing_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label for="billing_address" class="block text-sm font-medium text-gray-700 mb-1">
                            Street Address *
                        </label>
                        <input type="text" 
                               id="billing_address"
                               wire:model.defer="billing_address"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 @error('billing_address') border-red-500 @enderror">
                        @error('billing_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="billing_city" class="block text-sm font-medium text-gray-700 mb-1">
                                City *
                            </label>
                            <input type="text" 
                                   id="billing_city"
                                   wire:model.defer="billing_city"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 @error('billing_city') border-red-500 @enderror">
                            @error('billing_city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="billing_state" class="block text-sm font-medium text-gray-700 mb-1">
                                State *
                            </label>
                            <input type="text" 
                                   id="billing_state"
                                   wire:model.defer="billing_state"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 @error('billing_state') border-red-500 @enderror">
                            @error('billing_state')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="billing_country" class="block text-sm font-medium text-gray-700 mb-1">
                                Country *
                            </label>
                            <input type="text" 
                                   id="billing_country"
                                   wire:model.defer="billing_country"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 @error('billing_country') border-red-500 @enderror">
                            @error('billing_country')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="billing_postal_code" class="block text-sm font-medium text-gray-700 mb-1">
                                Postal Code *
                            </label>
                            <input type="text" 
                                   id="billing_postal_code"
                                   wire:model.defer="billing_postal_code"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 @error('billing_postal_code') border-red-500 @enderror">
                            @error('billing_postal_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Order Notes -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Notes (Optional)</h2>
                <textarea 
                    id="notes"
                    wire:model.defer="notes"
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                    placeholder="Add special instructions for your order..."></textarea>
            </div>
        </div>
        
        <!-- Right Column - Order Summary -->
        <div>
            <!-- Order Summary -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6 sticky top-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-200">
                    Order Summary
                </h2>
                
                <!-- Cart Items -->
                <div class="space-y-4 mb-6">
                    @foreach($cart as $item)
                    <div class="flex items-center py-3 border-b border-gray-100">
                        <div class="flex-shrink-0 w-16 h-16 bg-gray-200 rounded-md overflow-hidden">
                            @if(isset($item['image']) && $item['image'])
                                <img src="{{ Storage::url($item['image']) }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="flex justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">{{ $item['name'] }}</h4>
                                    <p class="text-sm text-gray-500">Qty: {{ $item['quantity'] }}</p>
                                </div>
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $currencySymbol }}{{ number_format($item['price'] * $item['quantity'], 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Free Shipping Progress -->
                @if(!$hasFreeShipping && $freeShippingThreshold > 0)
                <div class="mb-6 p-4 bg-blue-50 border border-blue-100 rounded-lg">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-medium text-blue-800">Free Delivery Available!</span>
                    </div>
                    <div class="mb-2">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $freeShippingProgress }}%"></div>
                        </div>
                    </div>
                    <p class="text-sm text-blue-700">
                        Add {{ $currencySymbol }}{{ number_format($remainingForFreeShipping, 2) }} more to get free delivery!
                    </p>
                </div>
                @endif
                
                <!-- Order Totals -->
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Subtotal</span>
                        <span class="text-sm text-gray-900">{{ $formattedSubtotal }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">{{ $requiresShipping ? 'Delivery Fee' : 'Delivery Fee' }}</span>
                        <span class="text-sm text-gray-900">
                            @if($hasFreeShipping)
                                <span class="text-green-600">Free</span>
                            @else
                                {{ $formattedShipping }}
                            @endif
                        </span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Platform Service Fee ({{ $serviceFeeRate }}%)</span>
                        <span class="text-sm text-gray-900">{{ $formattedTax }}</span>
                    </div>
                    
                    <div class="pt-3 border-t border-gray-200 flex justify-between">
                        <span class="text-base font-semibold text-gray-900">Total</span>
                        <span class="text-base font-semibold text-gray-900">{{ $formattedTotal }}</span>
                    </div>
                </div>
                
                <!-- Payment Method -->
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Payment Method</h3>

                    @if($checkoutError)
                        <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3">
                            <div class="flex items-start gap-3">
                                <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-red-800">Payment needs your attention</p>
                                    <p class="mt-1 text-sm text-red-700">{{ $checkoutError }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($payment_method === 'wallet' && $walletBalance < $total)
                        <div class="mb-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3">
                            <div class="flex items-start gap-3">
                                <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-amber-900">Wallet balance is below this order total</p>
                                    <p class="mt-1 text-sm text-amber-800">
                                        Available: {{ $formattedWalletBalance }}.
                                        Order total: {{ $formattedTotal }}.
                                    </p>
                                    <a href="{{ route('wallet.index') }}" class="mt-2 inline-flex text-sm font-semibold text-amber-900 underline underline-offset-2 hover:text-amber-700">
                                        Fund wallet
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-3">
                        @if(in_array('paystack', $availablePaymentMethods))
                        <div class="flex items-center p-3 border rounded-lg {{ $payment_method === 'paystack' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
                            <input type="radio" 
                                   id="paystack"
                                   name="payment_method"
                                   wire:model.change="payment_method"
                                   value="paystack"
                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300">
                            <label for="paystack" class="ml-3 flex-1 cursor-pointer">
                                <span class="text-sm font-medium text-gray-900">Pay with Paystack</span>
                                <span class="text-gray-500 text-xs block">Secure online payment via card, bank transfer, or USSD</span>
                            </label>
                            <span class="inline-flex items-center rounded-full bg-slate-900 px-3 py-1 text-xs font-semibold tracking-[0.2em] text-white">
                                PAYSTACK
                            </span>
                        </div>
                        @endif
                        
                        @if(in_array('cash_on_delivery', $availablePaymentMethods))
                        <div class="flex items-center p-3 border rounded-lg {{ $payment_method === 'cash_on_delivery' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
                            <input type="radio" 
                                   id="cash_on_delivery"
                                   name="payment_method"
                                   wire:model.change="payment_method"
                                   value="cash_on_delivery"
                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300">
                            <label for="cash_on_delivery" class="ml-3 flex-1 cursor-pointer">
                                <span class="text-sm font-medium text-gray-900">Cash on Delivery</span>
                                <span class="text-gray-500 text-xs block">Pay with cash when your order is delivered</span>
                            </label>
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        @endif

                        @if(in_array('wallet', $availablePaymentMethods))
                        <div class="flex items-center p-3 border rounded-lg {{ $payment_method === 'wallet' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
                            <input type="radio"
                                   id="wallet"
                                   name="payment_method"
                                   wire:model.change="payment_method"
                                   value="wallet"
                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300">
                            <label for="wallet" class="ml-3 flex-1 cursor-pointer">
                                <span class="text-sm font-medium text-gray-900">Wallet Balance</span>
                                <span class="text-gray-500 text-xs block">Available balance: {{ $formattedWalletBalance }}</span>
                            </label>
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2m0-6h2a2 2 0 012 2v2a2 2 0 01-2 2h-2m0-6h-4a2 2 0 100 4h4" />
                            </svg>
                        </div>
                        @endif
                    </div>
                    @error('payment_method')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Submit Button -->
                <button 
                    type="button"
                    wire:click="submitOrder"
                    wire:loading.attr="disabled"
                    @if($payment_method === 'wallet' && $walletBalance < $total) disabled @endif
                    wire:loading.class="opacity-50 cursor-not-allowed"
                    class="w-full bg-red-600 text-white py-3 px-4 rounded-md font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out disabled:cursor-not-allowed disabled:opacity-60">
                    <span wire:loading.remove>
                        @if($payment_method === 'paystack')
                            Pay {{ $formattedTotal }} with Paystack
                        @elseif($payment_method === 'wallet')
                            Pay {{ $formattedTotal }} from Wallet
                        @elseif($payment_method === 'cash_on_delivery')
                            Place Order (Cash on Delivery)
                        @else
                            Proceed to Payment
                        @endif
                    </span>
                    <span wire:loading>
                        <svg class="animate-spin h-5 w-5 mx-auto text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
                
                <!-- Return to Cart -->
                <div class="mt-4 text-center">
                    <a href="{{ route('cart') }}" class="text-sm text-red-600 hover:text-red-800">
                        ← Return to cart
                    </a>
                </div>
                
                <!-- Security Notice -->
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <div class="flex items-center justify-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span>Your payment information is secure and encrypted</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
