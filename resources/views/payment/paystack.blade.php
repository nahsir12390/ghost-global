@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-8 mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Complete Payment</h1>
            <p class="text-gray-600">Order #{{ $order->id }} - N{{ number_format($order->total, 2) }}</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-8">
            <form id="paymentForm">
                <input type="hidden" id="email" name="email" value="{{ Auth::user()->email }}">
                <input type="hidden" id="amount" name="amount" value="{{ (int) ($order->total * 100) }}">
                <input type="hidden" id="reference" name="reference" value="{{ $reference }}">
                <input type="hidden" id="orderId" name="order_id" value="{{ $order->id }}">

                <div class="mb-8 rounded-lg border border-blue-200 bg-blue-50 p-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-blue-900">Secure Payment</h3>
                            <p class="mt-1 text-sm text-blue-700">Your payment information is encrypted and secure. You will be redirected to Paystack to complete your payment.</p>
                        </div>
                    </div>
                </div>

                <div class="mb-8 rounded-lg bg-gray-50 p-6">
                    <h3 class="mb-4 font-semibold text-gray-900">Order Details</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal:</span>
                            <span>N{{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Shipping:</span>
                            <span>N{{ number_format($order->shipping, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Platform Service Fee:</span>
                            <span>N{{ number_format($order->tax, 2) }}</span>
                        </div>
                        <hr>
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total Amount:</span>
                            <span class="text-green-600">N{{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <button
                    type="button"
                    id="paystack-button"
                    onclick="payWithPaystack()"
                    class="w-full rounded-lg bg-blue-600 px-4 py-3 font-bold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    Pay N{{ number_format($order->total, 2) }} with Paystack
                </button>

                <div class="mt-6 text-center">
                    <a href="{{ route('payment.index', $order) }}" class="font-semibold text-gray-600 hover:text-gray-900">Back to payment methods</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
const checkStatusUrl = @json(route('payment.check-status'));
const verifyPaymentUrl = @json(route('payment.verify'));
const paymentFailedUrl = @json(route('payment.failed'));

function payWithPaystack() {
    const button = document.getElementById('paystack-button');
    const email = document.getElementById('email').value;
    const amount = document.getElementById('amount').value;
    const reference = document.getElementById('reference').value;
    const orderId = document.getElementById('orderId').value;
    const originalText = button.textContent.trim();

    if (button.disabled) {
        return;
    }

    let pollCount = 0;
    let paymentVerified = false;
    let pollInterval = null;

    button.disabled = true;
    button.textContent = 'Opening secure payment...';

    const handler = PaystackPop.setup({
        key: '{{ $publicKey }}',
        email: email,
        amount: amount,
        ref: reference,
        currency: 'NGN',
        callback: function(response) {
            paymentVerified = true;
            clearInterval(pollInterval);
            verifyPayment(response.reference || reference, orderId, originalText);
        },
        onClose: function() {
            clearInterval(pollInterval);

            if (!paymentVerified) {
                button.disabled = false;
                button.textContent = originalText;
            }
        }
    });

    handler.openIframe();
    button.textContent = 'Waiting for payment confirmation...';

    pollInterval = setInterval(() => {
        pollCount++;

        fetch(checkStatusUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                reference: reference,
                order_id: orderId
            })
        })
        .then(response => response.ok ? response.json() : null)
        .then(data => {
            if (data && data.success && !paymentVerified) {
                paymentVerified = true;
                clearInterval(pollInterval);
                verifyPayment(reference, orderId, originalText);
            }
        })
        .catch(() => {});

        if (pollCount >= 60) {
            clearInterval(pollInterval);

            if (!paymentVerified) {
                button.disabled = false;
                button.textContent = originalText;
                showMessage('Payment confirmation is taking longer than expected. If you were debited, please check your orders again shortly.', 'error');
            }
        }
    }, 1000);
}

function verifyPayment(reference, orderId, originalText) {
    const button = document.getElementById('paystack-button');

    button.disabled = true;
    button.textContent = 'Verifying payment...';

    fetch(verifyPaymentUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            reference: reference,
            order_id: orderId
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(() => {
                throw new Error('Unable to verify payment right now. Please try again.');
            });
        }

        return response.json();
    })
    .then(data => {
        if (data.success) {
            showMessage('Payment verified successfully. Redirecting...', 'success');
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1200);
            return;
        }

        button.disabled = false;
        button.textContent = originalText;
        showMessage(data.message || 'Payment verification failed.', 'error');
    })
    .catch(error => {
        button.disabled = false;
        button.textContent = originalText;
        showMessage('Error verifying payment: ' + error.message, 'error');

        setTimeout(() => {
            window.location.href = paymentFailedUrl;
        }, 1800);
    });
}

function showMessage(message, type) {
    const alertClass = type === 'error'
        ? 'bg-red-100 border-red-400 text-red-700'
        : 'bg-green-100 border-green-400 text-green-700';

    const alert = document.createElement('div');
    alert.className = `fixed right-4 top-4 z-50 max-w-md rounded border px-4 py-3 shadow ${alertClass}`;
    alert.innerText = message;
    document.body.appendChild(alert);

    setTimeout(() => alert.remove(), 5000);
}
</script>
@endsection
