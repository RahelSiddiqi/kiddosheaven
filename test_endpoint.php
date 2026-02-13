<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Category;
use App\Models\ProductAttribute;
use Illuminate\Support\Facades\DB;

// Find a category with attributes
$category = Category::whereHas('attributes')->first();

if (!$category) {
    echo "No category with attributes found\n";
    exit;
}

echo "Category: {$category->name} (ID: {$category->id})\n";

$ids = collect([$category->id])->merge($category->ancestors()->pluck('id'))->unique()->values();

echo "\nCategory IDs for query: " . $ids->implode(', ') . "\n";

// Simulate the controller query
$attributes = ProductAttribute::whereHas('categories', function ($query) use ($ids) {
    $query->whereIn('categories.id', $ids);
})
    ->where('use_for_variants', true)
    ->with('values')
    ->orderBy('sort_order')
    ->get();

echo "\n✓ Variant Attributes returned:\n";
foreach ($attributes as $attr) {
    echo "- {$attr->name} (ID: {$attr->id})\n";
}
?>
