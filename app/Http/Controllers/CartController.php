<?php

namespace App\Http\Controllers;

use App\Helpers\SettingsHelper;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        return view('cart', [
            'cartData' => $this->buildCartPayload(),
        ]);
    }

    public function summary(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'cart' => $this->buildCartPayload(),
        ]);
    }

    public function add(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $product = Product::query()->find($validated['product_id']);

        if (! $product || ! $product->is_active) {
            return $this->errorResponse('Product not found.', 404);
        }

        if (! $product->isPurchasable()) {
            return $this->errorResponse($product->unavailableReason(), 422);
        }

        if ($product->tracksInventory() && $product->quantity <= 0) {
            return $this->errorResponse('Product is out of stock.', 422);
        }

        $quantityToAdd = (int) ($validated['quantity'] ?? 1);
        $cart = $this->getCart();
        $currentQuantity = (int) ($cart[$product->id]['quantity'] ?? 0);
        $newQuantity = $currentQuantity + $quantityToAdd;

        if ($product->tracksInventory() && $newQuantity > $product->quantity) {
            return $this->errorResponse('Only '.$product->quantity.' items in stock.', 422);
        }

        $cart[$product->id] = $this->makeCartItem($product, $newQuantity);
        $this->putCart($cart);

        return $this->successResponse(
            $quantityToAdd > 1 ? 'Added items to cart!' : 'Added to cart!',
            $product->id
        );
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $productId = (int) $validated['product_id'];
        $quantity = (int) $validated['quantity'];
        $cart = $this->getCart();

        if (! isset($cart[$productId])) {
            return $this->errorResponse('Cart item not found.', 404);
        }

        if ($quantity === 0) {
            unset($cart[$productId]);
            $this->putCart($cart);

            return $this->successResponse('Item removed from cart!', $productId);
        }

        $product = Product::query()->find($productId);

        if (! $product || ! $product->is_active) {
            return $this->errorResponse('Product not found.', 404);
        }

        if (! $product->isPurchasable()) {
            return $this->errorResponse($product->unavailableReason(), 422);
        }

        if ($product->tracksInventory() && $quantity > $product->quantity) {
            return $this->errorResponse('Only '.$product->quantity.' items in stock.', 422);
        }

        $cart[$productId] = $this->makeCartItem($product, $quantity);
        $this->putCart($cart);

        return $this->successResponse('Quantity updated!', $productId);
    }

    public function remove(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer'],
        ]);

        $productId = (int) $validated['product_id'];
        $cart = $this->getCart();

        if (! isset($cart[$productId])) {
            return $this->errorResponse('Cart item not found.', 404);
        }

        unset($cart[$productId]);
        $this->putCart($cart);

        return $this->successResponse('Item removed from cart!', $productId);
    }

    public function clear(): JsonResponse
    {
        $this->putCart([]);

        return $this->successResponse('Cart cleared!');
    }

    private function successResponse(string $message, ?int $productId = null): JsonResponse
    {
        $payload = $this->buildCartPayload();
        $item = $productId !== null ? ($payload['items'][(string) $productId] ?? null) : null;

        return response()->json([
            'success' => true,
            'message' => $message,
            'cart' => $payload,
            'summary' => $payload['summary'],
            'item' => $item,
            'product_id' => $productId,
        ]);
    }

    private function errorResponse(string $message, int $status = 422): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }

    private function buildCartPayload(): array
    {
        $items = [];
        $cart = $this->getCart();

        foreach ($cart as $productId => $item) {
            $product = Product::query()->find($productId);
            $normalizedItem = $this->normalizeCartItem((int) $productId, $item, $product);
            $items[(string) $productId] = $normalizedItem;
            $cart[$productId] = $normalizedItem;
        }

        $this->putCart($cart);

        return [
            'items' => $items,
            'summary' => $this->buildSummary($items),
        ];
    }

    private function buildSummary(array $items): array
    {
        $subtotal = 0;
        $count = 0;
        $unavailableCount = 0;
        $requiresShipping = false;

        foreach ($items as $item) {
            $subtotal += (float) $item['price'] * (int) $item['quantity'];
            $count += (int) $item['quantity'];
            $requiresShipping = $requiresShipping || (bool) ($item['requires_shipping'] ?? false);
            if (! ($item['is_purchasable'] ?? true)) {
                $unavailableCount++;
            }
        }

        $breakdown = $requiresShipping
            ? SettingsHelper::calculateOrderBreakdown($subtotal)
            : [
                'subtotal' => round($subtotal, 2),
                'service_fee_rate' => (float) SettingsHelper::platformServiceFeeRate($subtotal),
                'service_fee' => round(($subtotal * (float) SettingsHelper::platformServiceFeeRate($subtotal)) / 100, 2),
                'shipping' => 0,
                'total' => round($subtotal + (($subtotal * (float) SettingsHelper::platformServiceFeeRate($subtotal)) / 100), 2),
            ];
        $freeShippingThreshold = (float) SettingsHelper::freeShippingThreshold();

        return [
            'count' => $count,
            'line_items' => count($items),
            'unavailable_count' => $unavailableCount,
            'can_checkout' => $unavailableCount === 0,
            'requires_shipping' => $requiresShipping,
            'subtotal' => $breakdown['subtotal'],
            'tax_rate' => $breakdown['service_fee_rate'],
            'tax' => $breakdown['service_fee'],
            'service_fee_rate' => $breakdown['service_fee_rate'],
            'service_fee' => $breakdown['service_fee'],
            'shipping' => $breakdown['shipping'],
            'total' => $breakdown['total'],
            'free_shipping_threshold' => $freeShippingThreshold,
            'free_shipping_remaining' => $freeShippingThreshold > 0 ? max($freeShippingThreshold - $subtotal, 0) : 0,
        ];
    }

    private function makeCartItem(Product $product, int $quantity): array
    {
        return $this->normalizeCartItem($product->id, [
            'quantity' => $quantity,
        ], $product);
    }

    private function normalizeCartItem(int $productId, array $item, ?Product $product = null): array
    {
        $quantity = max((int) ($item['quantity'] ?? 1), 1);
        $price = (float) ($product?->price ?? $item['price'] ?? 0);
        $image = $product?->main_image ?? $item['image'] ?? null;

        if (! $image && $product && is_array($product->images) && isset($product->images[0])) {
            $image = $product->images[0];
        }

        return [
            'product_id' => $productId,
            'name' => $product?->name ?? $item['name'] ?? 'Product',
            'price' => $price,
            'quantity' => $quantity,
            'image' => $image,
            'image_url' => $image ? asset('storage/'.$image) : null,
            'slug' => $product?->slug ?? $item['slug'] ?? '#',
            'stock' => $product?->tracksInventory() ? (int) ($product?->quantity ?? $item['stock'] ?? $quantity) : 9999,
            'line_total' => round($price * $quantity, 2),
            'product_type' => $product?->product_type ?? $item['product_type'] ?? Product::TYPE_PHYSICAL,
            'requires_shipping' => $product?->requiresShipping() ?? ($item['requires_shipping'] ?? true),
            'is_purchasable' => $product?->isPurchasable() ?? false,
            'unavailable_reason' => $product?->unavailableReason(),
        ];
    }

    private function getCart(): array
    {
        return session()->get('cart', []);
    }

    private function putCart(array $cart): void
    {
        session()->put('cart', $cart);
    }
}
