@extends('layouts.app')

@section('title', 'My Wallet')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-1 space-y-6">
            <div class="overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-red-700 p-6 text-white shadow-xl">
                <p class="text-sm uppercase tracking-[0.3em] text-white/70">Store Wallet</p>
                <h1 class="mt-3 text-3xl font-semibold">{{ \App\Helpers\SettingsHelper::currency($wallet->balance) }}</h1>
                <p class="mt-3 max-w-sm text-sm text-white/75">
                    Use your wallet for faster checkout, refunds, and future rewards inside the app.
                </p>
            </div>

            <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="mb-5">
                    <h2 class="text-lg font-semibold text-gray-900">Fund Wallet</h2>
                    <p class="mt-1 text-sm text-gray-500">Add money with Paystack and spend it later at checkout.</p>
                </div>

                @if(session('success'))
                    <div class="mb-4 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('wallet.top-up') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Top-up Amount</label>
                        <input type="number"
                               id="amount"
                               name="amount"
                               min="{{ $minimumTopup }}"
                               step="0.01"
                               value="{{ old('amount', $minimumTopup) }}"
                               class="w-full rounded-2xl border border-gray-300 px-4 py-3 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100">
                        <p class="mt-2 text-sm text-gray-500">Minimum top-up: {{ \App\Helpers\SettingsHelper::currency($minimumTopup) }}</p>
                        @error('amount')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-red-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-red-700">
                        Continue with Paystack
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="rounded-3xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-5">
                    <h2 class="text-lg font-semibold text-gray-900">Transaction History</h2>
                    <p class="mt-1 text-sm text-gray-500">Every wallet credit and debit is recorded here.</p>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse($transactions as $transaction)
                        <div class="px-6 py-5">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl {{ $transaction->direction === 'credit' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            @if($transaction->direction === 'credit')
                                                +
                                            @else
                                                -
                                            @endif
                                        </span>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ ucwords(str_replace('_', ' ', $transaction->type)) }}</p>
                                            <p class="text-sm text-gray-500">{{ $transaction->description ?: 'Wallet transaction' }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-3 space-y-1 text-sm text-gray-500">
                                        <p>Reference: {{ $transaction->reference }}</p>
                                        <p>Status: {{ ucfirst($transaction->status) }}</p>
                                        <p>{{ $transaction->created_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                </div>
                                <div class="text-left sm:text-right">
                                    <p class="text-lg font-semibold {{ $transaction->direction === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $transaction->direction === 'credit' ? '+' : '-' }}{{ \App\Helpers\SettingsHelper::currency($transaction->amount) }}
                                    </p>
                                    <p class="mt-1 text-sm text-gray-500">Balance after: {{ \App\Helpers\SettingsHelper::currency($transaction->balance_after) }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-16 text-center">
                            <p class="text-lg font-semibold text-gray-900">No wallet activity yet</p>
                            <p class="mt-2 text-sm text-gray-500">Once you fund or spend from your wallet, the history will show up here.</p>
                        </div>
                    @endforelse
                </div>

                @if($transactions->hasPages())
                    <div class="border-t border-gray-100 px-6 py-4">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
