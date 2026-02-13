<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n=== PRODUCT ATTRIBUTES ===\n";
$attrs = DB::table('product_attributes')
  ->select('id', 'name', 'use_for_variants')
  ->orderBy('name')
  ->get();

foreach($attrs as $a) {
  $variant = $a->use_for_variants ? '✓ VARIANT' : '✗ NON-VARIANT';
  echo "ID {$a->id} | {$a->name} | {$variant}\n";
}

echo "\n=== CATEGORY 1 ATTRIBUTES ===\n";
$catAttrs = DB::table('category_attributes')
  ->where('category_id', 1)
  ->select('product_attribute_id')
  ->get();

foreach($catAttrs as $ca) {
  $attr = DB::table('product_attributes')->find($ca->product_attribute_id);
  $variant = $attr->use_for_variants ? '✓ VARIANT' : '✗ NON-VARIANT';
  echo "- {$attr->name} | {$variant}\n";
}
