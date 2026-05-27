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
        $variantRows = collect($data['variant_quantities'] ?? [])
            ->map(fn ($qty) => (int) $qty)
            ->filter(fn ($qty) => $qty > 0);

        $variantDetails = collect();
        if ($variantRows->isNotEmpty()) {
            $variants = ProductVariant::whereIn('id', $variantRows->keys()->all())->get()->keyBy('id');
            $quantity = max($variantRows->sum(), $product->min_order_qty);
            $baseTotal = $variantRows->sum(function (int $rowQty, int|string $variantId) use ($variants, $product, $width, $height, $variantDetails) {
                $rowVariant = $variants->get((int) $variantId);
                $base = $rowVariant?->price ?: $product->base_price;
                $subtotal = $this->baseSubtotal($product, $base, $rowQty, $width, $height);
                $variantDetails->push([
                    'variant' => $rowVariant?->name,
                    'quantity' => $rowQty,
                    'price' => $base,
                    'subtotal' => $subtotal,
                ]);

                return $subtotal;
            });
        } else {
            $base = $variant?->price ?: $product->base_price;
            $baseTotal = $this->baseSubtotal($product, $base, $quantity, $width, $height);
        }

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
                'variants' => $variantDetails->values()->all(),
                'options' => $options->pluck('name')->values()->all(),
                'width' => $width,
                'height' => $height,
                'quantity' => $quantity,
            ],
        ];
    }

    private function baseSubtotal(Product $product, float $base, int $quantity, float $width, float $height): float
    {
        return match ($product->pricing_type) {
            'meter' => $base * $width * $quantity,
            'square_meter' => $base * max($width * $height, 1) * $quantity,
            'package' => $base * max($quantity, 1),
            'rim' => $base * $quantity,
            'manual' => 0,
            default => $base * $quantity,
        };
    }
}
