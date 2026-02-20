@props(['product', 'variants'])

@if ($product->product_type === 'variable' && $variants->isNotEmpty())
    @php
        // Group variants by attribute for a nicer selector
        $attributeGroups = [];
        foreach ($variants as $variant) {
            foreach ($variant->variantAttributes as $va) {
                $attrName = $va->attribute->name ?? 'Option';
                $attrId = $va->product_attribute_id;
                $valName = $va->attributeValue->value ?? '';
                $valId = $va->product_attribute_value_id;
                if (!isset($attributeGroups[$attrId])) {
                    $attributeGroups[$attrId] = [
                        'name' => $attrName,
                        'values' => [],
                    ];
                }
                if (!isset($attributeGroups[$attrId]['values'][$valId])) {
                    $attributeGroups[$attrId]['values'][$valId] = $valName;
                }
            }
        }

        // Product-level discount
        $prodDiscountType  = $product->discount_type ?? 'percentage';
        $prodDiscountValue = (float) ($product->discount_price ?? 0);
        $hasProductDiscount = $prodDiscountValue > 0 && (
            $prodDiscountType === 'percentage' ? $prodDiscountValue < 100 : true
        );
        $calcDiscountedPrice = function(float $price) use ($prodDiscountType, $prodDiscountValue, $hasProductDiscount): float {
            if (!$hasProductDiscount) return $price;
            if ($prodDiscountType === 'percentage') return round($price * (1 - $prodDiscountValue / 100));
            return max(0, round($price - $prodDiscountValue));
        };

        // Color name → CSS hex map
        $colorMap = [
            'red'=>'#ef4444','blue'=>'#3b82f6','green'=>'#22c55e','yellow'=>'#eab308',
            'orange'=>'#f97316','purple'=>'#a855f7','pink'=>'#ec4899','white'=>'#ffffff',
            'black'=>'#000000','gray'=>'#6b7280','grey'=>'#6b7280','brown'=>'#92400e',
            'beige'=>'#d3b896','cream'=>'#fffdd0','navy'=>'#1e3a5f','teal'=>'#14b8a6',
            'cyan'=>'#06b6d4','magenta'=>'#d946ef','lime'=>'#84cc16','indigo'=>'#6366f1',
            'violet'=>'#7c3aed','maroon'=>'#9f1239','olive'=>'#65a30d','coral'=>'#fb7185',
            'salmon'=>'#fca5a5','gold'=>'#f59e0b','silver'=>'#9ca3af','turquoise'=>'#2dd4bf',
            'khaki'=>'#c3a26d','lavender'=>'#c4b5fd','peach'=>'#fcd5b0','mint'=>'#6ee7b7',
            'hot pink'=>'#ec4899','hotpink'=>'#ec4899','baby pink'=>'#f9a8d4','babypink'=>'#f9a8d4',
            'light pink'=>'#fbcfe8','dark pink'=>'#be185d','rose'=>'#f43f5e',
            'classic beige'=>'#d3b896','light beige'=>'#f5f0e8','dark beige'=>'#b5975c',
            'off white'=>'#fafaf9','offwhite'=>'#fafaf9','ivory'=>'#fffff0',
            'sky blue'=>'#7dd3fc','skyblue'=>'#7dd3fc','light blue'=>'#bfdbfe','dark blue'=>'#1d4ed8',
            'royal blue'=>'#2563eb','baby blue'=>'#bfdbfe',
            'light green'=>'#bbf7d0','dark green'=>'#15803d','forest green'=>'#166534',
            'light yellow'=>'#fef9c3','dark yellow'=>'#ca8a04',
        ];
        $resolveColor = function(string $name) use ($colorMap): string {
            if (preg_match('/^#[0-9a-fA-F]{3,6}$/', $name) || preg_match('/^rgb/', $name)) return $name;
            $lower = strtolower(trim($name));
            return $colorMap[$lower] ?? $colorMap[str_replace(' ','',$lower)] ?? $lower;
        };

        $defaultVariant = $variants->firstWhere('is_default', true) ?? $variants->first();
        $variantData = $variants->map(fn($v) => [
            'id' => $v->id,
            'price' => (float) $v->price,
            'discounted_price' => $calcDiscountedPrice((float) $v->price),
            'stock' => $v->available_quantity,
            'in_stock' => $v->is_in_stock,
            'low_stock' => $v->is_low_stock,
            'image' => $v->image ? asset('storage/' . $v->image) : null,
            'sku' => $v->sku,
            'attrs' => $v->variantAttributes->pluck('product_attribute_value_id', 'product_attribute_id')->toArray(),
        ])->values();
    @endphp

    <div class="space-y-4"
        x-data="{
            variants: {{ Js::from($variantData) }},
            selected: {{ Js::from($defaultVariant->variantAttributes->pluck('product_attribute_value_id', 'product_attribute_id')->toArray()) }},
            hasDiscount: {{ $hasProductDiscount ? 'true' : 'false' }},
            currentVariant: {{ Js::from([
                'id' => $defaultVariant->id,
                'price' => (float) $defaultVariant->price,
                'discounted_price' => $calcDiscountedPrice((float) $defaultVariant->price),
                'stock' => $defaultVariant->available_quantity,
                'in_stock' => $defaultVariant->is_in_stock,
                'low_stock' => $defaultVariant->is_low_stock,
            ]) }},
            selectAttr(attrId, valId) {
                this.selected[attrId] = valId;
                this.findVariant();
            },
            findVariant() {
                const match = this.variants.find(v => {
                    return Object.entries(this.selected).every(([attrId, valId]) => {
                        return String(v.attrs[attrId]) === String(valId);
                    });
                });
                if (match) {
                    this.currentVariant = match;
                    document.querySelector('input[name=variant_id]').value = match.id;
                    if (match.image) {
                        window.dispatchEvent(new CustomEvent('variant-image-change', { detail: { src: match.image } }));
                    }
                }
            }
        }">

        {{-- Attribute Selectors --}}
        @foreach ($attributeGroups as $attrId => $group)
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    {{ $group['name'] }}:
                    <span class="font-normal text-gray-500" x-text="(() => {
                        const vals = {{ Js::from($group['values']) }};
                        return vals[selected[{{ $attrId }}]] || '';
                    })()"></span>
                </label>
                <div class="flex flex-wrap gap-2">
                    @foreach ($group['values'] as $valId => $valName)
                        @php
                            $isColor = in_array(strtolower($group['name']), ['color', 'colour', 'রঙ']);
                        @endphp
                        @if ($isColor)
                            <button type="button"
                                @click="selectAttr({{ $attrId }}, {{ $valId }})"
                                :class="selected[{{ $attrId }}] == {{ $valId }}
                                    ? 'ring-2 ring-primary ring-offset-2 border-primary'
                                    : 'border-gray-200 hover:border-gray-400'"
                                class="w-9 h-9 rounded-full border-2 transition-all flex items-center justify-center"
                                title="{{ $valName }}">
                                <span class="w-6 h-6 rounded-full border border-black/10" style="background-color: {{ $resolveColor($valName) }}"></span>
                            </button>
                        @else
                            <button type="button"
                                @click="selectAttr({{ $attrId }}, {{ $valId }})"
                                :class="selected[{{ $attrId }}] == {{ $valId }}
                                    ? 'border-primary bg-primary/5 text-primary font-semibold'
                                    : 'border-gray-200 text-gray-700 hover:border-gray-400'"
                                class="px-4 py-2 rounded-lg border-2 text-sm transition-all">
                                {{ $valName }}
                            </button>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach

        {{-- Dynamic Variant Price & Stock --}}
        <div class="flex items-center gap-3 pt-2">
            <template x-if="hasDiscount">
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-primary" x-text="'৳' + currentVariant.discounted_price.toLocaleString()"></span>
                    <span class="text-base text-gray-400 line-through" x-text="'৳' + currentVariant.price.toLocaleString()"></span>
                </div>
            </template>
            <template x-if="!hasDiscount">
                <span class="text-2xl font-bold text-primary" x-text="'৳' + currentVariant.price.toLocaleString()"></span>
            </template>
            <template x-if="!currentVariant.in_stock">
                <span class="text-sm font-medium text-red-600 bg-red-50 px-2.5 py-1 rounded-full">Out of Stock</span>
            </template>
            <template x-if="currentVariant.in_stock && currentVariant.low_stock">
                <span class="text-sm font-medium text-orange-600 bg-orange-50 px-2.5 py-1 rounded-full"
                    x-text="'Only ' + currentVariant.stock + ' left'"></span>
            </template>
            <template x-if="currentVariant.in_stock && !currentVariant.low_stock">
                <span class="text-sm font-medium text-green-600 bg-green-50 px-2.5 py-1 rounded-full">In Stock</span>
            </template>
        </div>
    </div>
@endif
