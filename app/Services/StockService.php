<?php

namespace App\Services;

use App\Models\Material;
use App\Models\Order;
use App\Models\ProductMaterial;

class StockService
{
    public function move(Material $material, string $type, float $quantity, ?Order $order = null, ?string $note = null): void
    {
        $before = (float) $material->current_stock;
        $after = match ($type) {
            'in' => $before + $quantity,
            'out' => max($before - $quantity, 0),
            default => $quantity,
        };

        $material->update(['current_stock' => $after]);
        $material->movements()->create([
            'order_id' => $order?->id,
            'movement_type' => $type,
            'quantity' => $quantity,
            'stock_before' => $before,
            'stock_after' => $after,
            'reference_type' => $order ? 'order' : 'manual',
            'reference_id' => $order?->id,
            'note' => $note,
            'created_by' => auth()->id(),
        ]);
    }

    public function reduceForOrder(Order $order): void
    {
        $usageRules = ProductMaterial::with('material')
            ->whereIn('product_id', $order->items->pluck('product_id')->unique())
            ->get()
            ->groupBy('product_id');

        if ($usageRules->isEmpty()) {
            $firstMaterial = Material::query()->where('current_stock', '>', 0)->first();
            if (! $firstMaterial) {
                return;
            }
            $usage = $order->items->sum(function ($item) {
                $area = max((float) $item->width * (float) $item->height, 1);
                return $area * max((int) $item->quantity, 1);
            });
            $this->move($firstMaterial, 'out', $usage, $order, 'Pengurangan otomatis produksi '.$order->order_number);
            return;
        }

        foreach ($order->items as $item) {
            foreach ($usageRules->get($item->product_id, collect()) as $rule) {
                $area = max((float) $item->width * (float) $item->height, 1);
                $quantity = match ($rule->usage_type) {
                    'per_meter' => $rule->usage_per_unit * max((float) $item->width, 1) * $item->quantity,
                    'per_square_meter' => $rule->usage_per_unit * $area * $item->quantity,
                    default => $rule->usage_per_unit * $item->quantity,
                };
                $this->move($rule->material, 'out', $quantity, $order, 'Pemakaian bahan '.$item->product_name);
            }
        }
    }
}
