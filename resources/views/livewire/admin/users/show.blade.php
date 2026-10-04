<div class="space-y-6">
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
                <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>

            </div>
            <p class="mt-1 text-sm text-gray-600">
                {{ 'Customer account, wallet, and referral review' }}
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.users.orders', $user) }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">View Orders</a>
            <a href="{{ route('admin.users.edit', $user) }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Edit {{ 'User' }}</a>
            <a href="{{ route('admin.users.index') }}" class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">Back to Users</a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <form wire:submit="update" class="space-y-6">
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-5 py-4">
                        <h2 class="text-lg font-semibold text-gray-900">{{ 'User Profile' }}</h2>
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


                    </div>
                </div>



                <div class="flex justify-end">
                    <button type="submit" class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700">Save Changes</button>
                </div>
            </form>

            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">{{ 'Recent Orders' }}</h2>
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
                                            <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ \App\Helpers\SettingsHelper::currency($order->total) }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-900">{{ ucfirst($order->status) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">{{ "This customer hasn't placed any orders yet." }}</p>
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
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ 'Customer' }}</p>
                    </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Email Status</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->email_verified_at ? 'Verified' : 'Unverified' }}</p>
                        </div>

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
                            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">{{ 'Spent' }}</p>
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



            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900">Admin Tools</h3>
                <div class="mt-4 space-y-3">
                    <a href="{{ route('admin.users.edit', $user) }}" class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50">Edit {{ 'User' }}</a>
                    <a href="{{ route('admin.users.orders', $user) }}" class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50">View Orders</a>
                    <button wire:click="toggleAdminStatus" class="w-full rounded-lg border border-purple-300 px-4 py-2.5 text-sm font-semibold text-purple-700 hover:bg-purple-50">{{ $user->is_admin ? 'Remove Admin' : 'Make Admin' }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
