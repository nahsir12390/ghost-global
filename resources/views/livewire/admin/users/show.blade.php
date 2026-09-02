<div class="space-y-6" x-data="{ vendorActive: @entangle('vendor_is_active').defer }">
    @php
        $verificationStatus = $user->verification_status ?? 'pending';
        $bankStatus = $user->bank_verification_status ?? 'pending';
        $statusClasses = [
            'approved' => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
            'verified' => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
            'rejected' => 'bg-red-100 text-red-700 ring-red-200',
            'pending' => 'bg-amber-100 text-amber-700 ring-amber-200',
        ];
    @endphp

    <div class="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">{{ $user->isVendor() ? ($user->store_name ?: $user->name) : $user->name }}</h1>
                @if($user->isVendor())
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $statusClasses[$verificationStatus] ?? $statusClasses['pending'] }}">
                        {{ ucfirst($verificationStatus) }}
                    </span>
                @endif
            </div>
            <p class="mt-1 text-sm text-gray-600">
                {{ $user->isVendor() ? 'Vendor request, payout, wallet, and referral review' : 'Customer account, wallet, and referral review' }}
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.users.orders', $user) }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">View Orders</a>
            <a href="{{ route('admin.users.edit', $user) }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Edit {{ $user->isVendor() ? 'Vendor' : 'User' }}</a>
            <a href="{{ route('admin.users.index') }}" class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">Back to Users</a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <form wire:submit="update" class="space-y-6">
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-5 py-4">
                        <h2 class="text-lg font-semibold text-gray-900">{{ $user->isVendor() ? 'Vendor Profile' : 'User Profile' }}</h2>
                    </div>

                    <div class="grid gap-5 p-5 md:grid-cols-2">
                        <div>
                            <label for="name" class="mb-2 block text-sm font-medium text-gray-700">Full Name</label>
                            <input id="name" type="text" wire:model.defer="name" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 @error('name') border-red-500 @enderror">
                            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="email" class="mb-2 block text-sm font-medium text-gray-700">Email</label>
                            <input id="email" type="email" wire:model.defer="email" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 @error('email') border-red-500 @enderror">
                            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        @if($user->isVendor())
                            <div>
                                <label for="store_name" class="mb-2 block text-sm font-medium text-gray-700">Store Name</label>
                                <input id="store_name" type="text" wire:model.defer="store_name" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 @error('store_name') border-red-500 @enderror">
                                @error('store_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        @endif
                        <div>
                            <label for="phone" class="mb-2 block text-sm font-medium text-gray-700">Phone</label>
                            <input id="phone" type="text" wire:model.defer="phone" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 @error('phone') border-red-500 @enderror">
                            @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label for="address" class="mb-2 block text-sm font-medium text-gray-700">Business Address</label>
                            <input id="address" type="text" wire:model.defer="address" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 @error('address') border-red-500 @enderror">
                            @error('address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        @if($user->isVendor())
                            <div class="md:col-span-2 rounded-lg border border-gray-200 bg-gray-50 p-4">
                                <label for="vendor_is_active" class="flex items-start gap-3">
                                    <input id="vendor_is_active" type="checkbox" x-model="vendorActive" class="mt-1 h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    <span>
                                        <span class="block text-sm font-semibold text-gray-900">Vendor is active</span>
                                        <span class="mt-1 block text-sm text-gray-500">Turn this off when the vendor is unavailable. Customers cannot order this vendor's products while inactive.</span>
                                    </span>
                                </label>
                            </div>
                        @endif
                    </div>
                </div>

                @if($user->isVendor())
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-200 px-5 py-4">
                            <h2 class="text-lg font-semibold text-gray-900">Payout Details</h2>
                        </div>

                        <div class="grid gap-5 p-5 md:grid-cols-2">
                            <div>
                                <label for="bank_name" class="mb-2 block text-sm font-medium text-gray-700">Bank Name</label>
                                <input id="bank_name" type="text" wire:model.defer="bank_name" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 @error('bank_name') border-red-500 @enderror">
                                @error('bank_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="bank_account_name" class="mb-2 block text-sm font-medium text-gray-700">Account Holder</label>
                                <input id="bank_account_name" type="text" wire:model.defer="bank_account_name" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 @error('bank_account_name') border-red-500 @enderror">
                                @error('bank_account_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="bank_account_number" class="mb-2 block text-sm font-medium text-gray-700">Account Number</label>
                                <input id="bank_account_number" type="text" wire:model.defer="bank_account_number" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 @error('bank_account_number') border-red-500 @enderror">
                                @error('bank_account_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="bank_verification_status" class="mb-2 block text-sm font-medium text-gray-700">Bank Status</label>
                                <select id="bank_verification_status" wire:model.change="bank_verification_status" class="w-full rounded-lg border border-gray-300 px-4 py-2.5">
                                    <option value="pending">Pending</option>
                                    <option value="verified">Verified</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-200 px-5 py-4">
                            <h2 class="text-lg font-semibold text-gray-900">Admin Notes</h2>
                        </div>
                        <div class="p-5">
                            <textarea id="verification_notes" wire:model.defer="verification_notes" rows="3" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 @error('verification_notes') border-red-500 @enderror" placeholder="Optional note for approval or rejection"></textarea>
                            @error('verification_notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                @endif

                <div class="flex justify-end">
                    <button type="submit" class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700">Save Changes</button>
                </div>
            </form>

            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $user->isVendor() ? 'Recent Vendor Orders' : 'Recent Orders' }}</h2>
                    <a href="{{ route('admin.users.orders', $user) }}" class="text-sm font-semibold text-red-600 hover:text-red-800">View all</a>
                </div>
                <div class="p-5">
                    @if($recentOrders->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-700">Order</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-700">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-700">Total</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-700">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($recentOrders as $order)
                                        <tr>
                                            <td class="px-4 py-3 text-sm font-semibold text-red-600"><a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a></td>
                                            <td class="px-4 py-3 text-sm text-gray-900">{{ $order->created_at->format('M d, Y') }}</td>
                                            <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ \App\Helpers\SettingsHelper::currency($user->isVendor() ? $order->items->sum('total') : $order->total) }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-900">{{ ucfirst($order->status) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">{{ $user->isVendor() ? 'This vendor has not sold any products yet.' : "This customer hasn't placed any orders yet." }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900">Review Summary</h3>
                <div class="mt-4 space-y-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Account Type</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $user->isVendor() ? 'Vendor' : 'Customer' }}</p>
                    </div>
                    @if($user->isVendor())
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Vendor Status</p>
                            <span class="mt-1 inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $statusClasses[$verificationStatus] ?? $statusClasses['pending'] }}">
                                {{ ucfirst($verificationStatus) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Bank Status</p>
                            <span class="mt-1 inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $statusClasses[$bankStatus] ?? $statusClasses['pending'] }}">
                                {{ ucfirst($bankStatus) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Submitted</p>
                            <p class="mt-1 text-sm text-gray-900">{{ optional($user->verification_submitted_at)->format('M d, Y H:i') ?: 'Not submitted' }}</p>
                        </div>
                    @else
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Email Status</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->email_verified_at ? 'Verified' : 'Unverified' }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Member Since</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Referral Code</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $user->getOrCreateReferralCode() }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Referred By</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $user->referredBy?->name ?: 'Direct signup' }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <div class="rounded-lg bg-blue-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Orders</p>
                            <p class="mt-2 text-2xl font-bold text-blue-900">{{ $stats['total_orders'] }}</p>
                        </div>
                        <div class="rounded-lg bg-emerald-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">{{ $user->isVendor() ? 'Sales' : 'Spent' }}</p>
                            <p class="mt-2 text-xl font-bold text-emerald-900">{{ \App\Helpers\SettingsHelper::currency($stats['total_spent']) }}</p>
                        </div>
                        <div class="rounded-lg bg-violet-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-violet-700">Wallet Balance</p>
                            <p class="mt-2 text-xl font-bold text-violet-900">{{ \App\Helpers\SettingsHelper::currency($stats['wallet_balance'] ?? 0) }}</p>
                        </div>
                        <div class="rounded-lg bg-amber-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Rewarded Referrals</p>
                            <p class="mt-2 text-xl font-bold text-amber-900">{{ $stats['rewarded_referrals_count'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900">Wallet & Referral Activity</h3>
                <div class="mt-4 space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-lg bg-gray-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Credits</p>
                            <p class="mt-2 text-lg font-bold text-gray-900">{{ \App\Helpers\SettingsHelper::currency($stats['wallet_credits'] ?? 0) }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Debits</p>
                            <p class="mt-2 text-lg font-bold text-gray-900">{{ \App\Helpers\SettingsHelper::currency($stats['wallet_debits'] ?? 0) }}</p>
                        </div>
                    </div>

                    <div class="rounded-lg bg-gray-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Referral Signups</p>
                        <p class="mt-2 text-lg font-bold text-gray-900">{{ $stats['referrals_count'] ?? 0 }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Recent Wallet Transactions</p>
                        <div class="mt-3 space-y-3">
                            @forelse($recentWalletTransactions as $transaction)
                                <div class="rounded-lg border border-gray-200 px-4 py-3">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $transaction->type)) }}</p>
                                            <p class="mt-1 text-xs text-gray-500">{{ $transaction->created_at->format('M d, Y H:i') }}</p>
                                        </div>
                                        <p class="text-sm font-semibold {{ $transaction->direction === 'credit' ? 'text-emerald-600' : 'text-gray-900' }}">
                                            {{ $transaction->direction === 'credit' ? '+' : '-' }}{{ \App\Helpers\SettingsHelper::currency($transaction->amount) }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">No wallet activity yet for this user.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            @if($user->isVendor())
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">Approval Actions</h3>
                    <div class="mt-4 space-y-3">
                        @if($user->verification_status !== 'approved')
                            <form action="{{ route('admin.users.verification', $user) }}" method="POST" class="space-y-3">
                                @csrf
                                <input type="hidden" name="verification_notes" value="{{ $verification_notes }}">
                                <button type="submit" name="verification_status" value="approved" class="w-full rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">Approve Vendor</button>
                                <button type="submit" name="verification_status" value="rejected" class="w-full rounded-lg border border-red-300 px-4 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-50">Reject Vendor</button>
                            </form>
                        @else
                            <p class="rounded-lg bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">Vendor account is approved.</p>
                        @endif

                        @if($user->bank_account_number && $user->bank_verification_status !== 'verified')
                            <form action="{{ route('admin.users.verification', $user) }}" method="POST" class="space-y-3 border-t border-gray-200 pt-3">
                                @csrf
                                @if($user->bank_verification_status === 'rejected')
                                    <button type="submit" name="bank_verification_status" value="pending" class="w-full rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Reset Bank Status</button>
                                @else
                                    <button type="submit" name="bank_verification_status" value="verified" class="w-full rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">Verify Bank Account</button>
                                @endif
                                <button type="submit" name="bank_verification_status" value="rejected" class="w-full rounded-lg border border-amber-300 px-4 py-2.5 text-sm font-semibold text-amber-700 hover:bg-amber-50">Reject Bank Account</button>
                            </form>
                        @elseif($user->bank_account_number)
                            <p class="rounded-lg bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">Bank account is verified.</p>
                        @else
                            <p class="rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-600">No bank account submitted yet.</p>
                        @endif
                    </div>
                </div>
            @endif

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900">Admin Tools</h3>
                <div class="mt-4 space-y-3">
                    <a href="{{ route('admin.users.edit', $user) }}" class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50">Edit {{ $user->isVendor() ? 'Vendor' : 'User' }}</a>
                    <a href="{{ route('admin.users.orders', $user) }}" class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50">View Orders</a>
                    <button wire:click="toggleAdminStatus" class="w-full rounded-lg border border-purple-300 px-4 py-2.5 text-sm font-semibold text-purple-700 hover:bg-purple-50">{{ $user->is_admin ? 'Remove Admin' : 'Make Admin' }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
