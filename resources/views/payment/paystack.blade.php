@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-8 mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Complete Payment</h1>
            <p class="text-gray-600">Order #{{ $order->order_number }} - ₦{{ number_format($order->total, 2) }}</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="mb-8 rounded-lg border border-blue-200 bg-blue-50 p-6">
                <h3 class="text-sm font-semibold text-blue-900">Secure Paystack Payment</h3>
                <p class="mt-1 text-sm text-blue-700">Your payment is confirmed on our server before your order is marked as paid.</p>
            </div>

            <div class="mb-8 rounded-lg bg-gray-50 p-6 space-y-3">
                <div class="flex justify-between"><span class="text-gray-600">Subtotal:</span><span>₦{{ number_format($order->subtotal, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-600">Shipping:</span><span>₦{{ number_format($order->shipping, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-600">Service fee:</span><span>₦{{ number_format($order->tax, 2) }}</span></div>
                <hr>
                <div class="flex justify-between text-lg font-bold"><span>Total:</span><span>₦{{ number_format($order->total, 2) }}</span></div>
            </div>

            <button type="button" id="paystack-button" class="w-full rounded-lg bg-blue-600 px-4 py-3 font-bold text-white hover:bg-blue-700 disabled:opacity-60">
                Pay ₦{{ number_format($order->total, 2) }} with Paystack
            </button>

            <div id="payment-message" class="mt-4 hidden rounded-lg p-4 text-sm"></div>

            <div class="mt-6 text-center">
                <a href="{{ route('payment.index', $order) }}" class="font-semibold text-gray-600 hover:text-gray-900">Back to payment methods</a>
            </div>
        </div>
    </div>
</div>

<script src="https://js.paystack.co/v2/inline.js"></script>
<script>
(() => {
    const button = document.getElementById('paystack-button');
    const message = document.getElementById('payment-message');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const reference = @js($reference);
    const orderId = @js($order->id);
    const verifyUrl = @js(route('payment.verify'));
    const originalText = button.textContent.trim();

    function showMessage(text, type = 'info') {
        message.classList.remove('hidden', 'bg-red-100', 'text-red-700', 'bg-green-100', 'text-green-700', 'bg-blue-100', 'text-blue-700');
        const classes = type === 'error' ? ['bg-red-100', 'text-red-700'] : type === 'success' ? ['bg-green-100', 'text-green-700'] : ['bg-blue-100', 'text-blue-700'];
        message.classList.add(...classes);
        message.textContent = text;
    }

    async function verifyPayment(transactionReference) {
        button.disabled = true;
        button.textContent = 'Verifying payment...';

        try {
            const response = await fetch(verifyUrl, {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf},
                body: JSON.stringify({reference: transactionReference, order_id: orderId})
            });
            const data = await response.json();

            if (response.ok && data.success) {
                showMessage('Payment verified successfully. Redirecting...', 'success');
                window.location.assign(data.redirect);
                return;
            }

            showMessage(data.message || 'Payment has not been confirmed yet. Please try again.', response.status === 202 ? 'info' : 'error');
        } catch (error) {
            showMessage('We could not verify the payment right now. If you were debited, check your order before paying again.', 'error');
        }

        button.disabled = false;
        button.textContent = originalText;
    }

    button.addEventListener('click', () => {
        if (button.disabled) return;
        button.disabled = true;
        button.textContent = 'Opening secure payment...';

        const popup = new PaystackPop();
        popup.newTransaction({
            key: @js($publicKey),
            email: @js($order->contact_email),
            amount: @js($amount),
            currency: @js($currency),
            reference: reference,
            metadata: {order_id: orderId, order_number: @js($order->order_number)},
            onSuccess: transaction => verifyPayment(transaction.reference || reference),
            onCancel: () => {
                button.disabled = false;
                button.textContent = originalText;
                showMessage('Payment window closed. Your order has not been marked as paid.');
            },
            onError: error => {
                button.disabled = false;
                button.textContent = originalText;
                showMessage(error?.message || 'Paystack could not start the payment.', 'error');
            }
        });
    });
})();
</script>
@endsection
