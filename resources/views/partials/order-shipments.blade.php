<section class="my-6 space-y-4 rounded-2xl border border-slate-200 bg-white p-6">
    <h2 class="text-xl font-semibold">Delivery &amp; tracking</h2>
    @if($order->estimated_delivery_from && $order->estimated_delivery_to)
        <p class="text-sm text-slate-700">Estimate at checkout: {{ $order->estimated_delivery_from->format('j M Y') }}–{{ $order->estimated_delivery_to->format('j M Y') }}.</p>
        <p class="text-sm text-slate-500">Dates are estimates. When available, updated dates for each parcel appear below. {{ $order->delivery_import_charges }}</p>
    @endif
    @forelse($order->shipments as $shipment)
        <article class="space-y-2 rounded-xl bg-slate-50 p-4">
            <div class="flex flex-wrap justify-between gap-3"><strong>{{ $shipment->reference }}</strong><span class="text-sm font-semibold text-blue-800">{{ \App\Models\Shipment::STATUSES[$shipment->status] ?? $shipment->status }}</span></div>
            <ul class="text-sm text-slate-700">@foreach($shipment->items as $item)<li>{{ $item->product_name }} × {{ $item->pivot->quantity }}</li>@endforeach</ul>
            @if($shipment->estimated_from && $shipment->estimated_to)<p class="text-sm">Latest estimated arrival: <strong>{{ $shipment->estimated_from->format('j M Y') }}–{{ $shipment->estimated_to->format('j M Y') }}</strong></p>@endif
            @if($shipment->customer_update)<p class="whitespace-pre-line text-sm text-slate-700">{{ $shipment->customer_update }}</p>@endif
            @if($shipment->carrier || $shipment->tracking_number)<p class="text-sm">{{ $shipment->carrier }} · {{ $shipment->tracking_number }}</p>@endif
            @if($shipment->tracking_url)<a href="{{ $shipment->tracking_url }}" target="_blank" rel="noopener noreferrer" class="inline-block text-sm font-semibold text-blue-700 underline">Track this parcel</a>@endif
        </article>
    @empty
        <p class="text-sm text-slate-600">Shipment details will appear here after your order is prepared. Items may arrive in separate parcels.</p>
    @endforelse
</section>
