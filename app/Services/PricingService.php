<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductVariant;

class PricingService
{
    public function calculate(Product $product, array $data): array
    {
        $quantity = max((int) ($data['quantity'] ?? $product->min_order_qty), $product->min_order_qty);
        $width = (float) ($data['width'] ?? 1);
        $height = (float) ($data['height'] ?? 1);
        $variant = ! empty($data['product_variant_id']) ? ProductVariant::find($data['product_variant_id']) : null;
        $base = $variant?->price ?: $product->base_price;

        $baseTotal = match ($product->pricing_type) {
            'meter' => $base * $width * $quantity,
            'square_meter' => $base * max($width * $height, 1) * $quantity,
            'package' => $base,
            'rim' => $base * $quantity,
            'manual' => 0,
            default => $base * $quantity,
        };

        $selectedOptions = collect($data['options'] ?? [])->filter()->values();
        $options = ProductOption::whereIn('id', $selectedOptions)->get();
        $optionTotal = $options->sum(function (ProductOption $option) use ($quantity, $width, $height) {
            return match ($option->calculation_type) {
                'per_qty' => $option->price_modifier * $quantity,
                'per_meter' => $option->price_modifier * $width * $quantity,
                'per_square_meter' => $option->price_modifier * max($width * $height, 1) * $quantity,
                default => $option->price_modifier,
            };
        });

        $discount = (float) ($data['discount'] ?? 0);
        $shipping = (float) ($data['shipping_cost'] ?? 0);
        $total = max($baseTotal + $optionTotal + $shipping - $discount, 0);

        return [
            'base_total' => $baseTotal,
            'option_total' => $optionTotal,
            'discount' => $discount,
            'shipping_cost' => $shipping,
            'total' => $total,
            'specifications' => [
                'variant' => $variant?->name,
                'options' => $options->pluck('name')->values()->all(),
                'width' => $width,
                'height' => $height,
                'quantity' => $quantity,
            ],
        ];
    }
}
