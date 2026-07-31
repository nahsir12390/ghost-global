@extends('layouts.app')

@section('title', 'Fund Wallet')

@section('content')
<div class="min-h-[70vh] bg-gray-50 py-10 px-4">
    <div class="mx-auto max-w-xl">
        <div class="rounded-3xl border border-gray-200 bg-white p-8 shadow-sm">
            <div class="mb-6">
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-red-600">Wallet Top-up</p>
                <h1 class="mt-3 text-3xl font-semibold text-gray-900">Complete your funding</h1>
                <p class="mt-2 text-sm text-gray-500">
                    You are adding {{ \App\Helpers\SettingsHelper::currency($transaction->amount) }} to your wallet.
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-5">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Reference</span>
                    <span class="font-medium text-gray-900">{{ $transaction->reference }}</span>
                </div>
                <div class="mt-3 flex items-center justify-between text-sm">
                    <span class="text-gray-500">Provider</span>
                    <span class="font-medium text-gray-900">Paystack</span>
                </div>
                <div class="mt-3 flex items-center justify-between text-sm">
                    <span class="text-gray-500">Amount</span>
                    <span class="font-semibold text-gray-900">{{ \App\Helpers\SettingsHelper::currency($transaction->amount) }}</span>
                </div>
            </div>

            <button id="paystack-wallet-button"
                    class="mt-6 inline-flex w-full items-center justify-center rounded-2xl bg-red-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60">
                Pay with Paystack
            </button>

            <a href="{{ route('wallet.index') }}" class="mt-4 block text-center text-sm font-medium text-gray-500 transition hover:text-red-600">
                Back to wallet
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const button = document.getElementById('paystack-wallet-button');

    if (!button) {
        return;
    }

    button.addEventListener('click', () => {
        button.disabled = true;

        const handler = PaystackPop.setup({
            key: @json($publicKey),
            email: @json(auth()->user()->email),
            amount: {{ $amount }},
            ref: @json($transaction->reference),
            metadata: {
                custom_fields: [{
                    display_name: 'Wallet Top-up',
                    variable_name: 'wallet_topup',
                    value: @json($transaction->reference),
                }],
            },
            callback: function(response) {
                window.location.href = @json(route('wallet.callback')) + '?reference=' + encodeURIComponent(response.reference);
            },
            onClose: function() {
                button.disabled = false;
            }
        });

        handler.openIframe();
    });
});
</script>
@endpush
