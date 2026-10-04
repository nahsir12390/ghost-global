<h1>Delivery update</h1>
<p>Order {{ $shipment->order->order_number }} · Shipment {{ $shipment->reference }}</p>
<p><strong>{{ \App\Models\Shipment::STATUSES[$shipment->status] }}</strong></p>
@if($shipment->estimated_from && $shipment->estimated_to)<p>Estimated arrival: {{ $shipment->estimated_from->format('j M Y') }}–{{ $shipment->estimated_to->format('j M Y') }}. These dates are estimates.</p>@endif
@if($shipment->customer_update)<p>{{ $shipment->customer_update }}</p>@endif
@if($shipment->carrier)<p>Courier: {{ $shipment->carrier }}</p>@endif
@if($shipment->tracking_number)<p>Tracking number: {{ $shipment->tracking_number }}</p>@endif
@if($shipment->tracking_url)<p><a href="{{ $shipment->tracking_url }}">Track this shipment</a></p>@endif
<ul>@foreach($shipment->items as $item)<li>{{ $item->product_name }} × {{ $item->pivot->quantity }}</li>@endforeach</ul>
<p><a href="{{ route('my.orders.show', $shipment->order) }}">View your order</a></p>
