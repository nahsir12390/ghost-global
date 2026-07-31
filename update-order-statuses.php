<?php
/**
 * Quick update script for order statuses
 * Run: php artisan tinker < update-order-statuses.php
 */

// Connect to database and update orders
use Illuminate\Support\Facades\DB;

echo "Updating order statuses...\n";

// pending → ordered
$updated = DB::table('orders')->where('status', 'pending')->update(['status' => 'ordered']);
echo "✓ Updated $updated 'pending' → 'ordered'\n";

// processing → confirmed
$updated = DB::table('orders')->where('status', 'processing')->update(['status' => 'confirmed']);
echo "✓ Updated $updated 'processing' → 'confirmed'\n";

// packed → picked_up
$updated = DB::table('orders')->where('status', 'packed')->update(['status' => 'picked_up']);
echo "✓ Updated $updated 'packed' → 'picked_up'\n";

// shipped → on_the_way
$updated = DB::table('orders')->where('status', 'shipped')->update(['status' => 'on_the_way']);
echo "✓ Updated $updated 'shipped' → 'on_the_way'\n";

// failed → cancelled
$updated = DB::table('orders')->where('status', 'failed')->update(['status' => 'cancelled']);
echo "✓ Updated $updated 'failed' → 'cancelled'\n";

// Check results
$stats = DB::table('orders')
    ->selectRaw('status, COUNT(*) as count')
    ->groupBy('status')
    ->get();

echo "\n✅ Final Order Status Distribution:\n";
foreach ($stats as $stat) {
    echo "   {$stat->status}: {$stat->count} orders\n";
}

echo "\n✅ Status migration completed successfully!\n";
?>
