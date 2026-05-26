<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Material;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function products(): StreamedResponse
    {
        return $this->csv('products.csv', ['Kode', 'Produk', 'Kategori', 'Layanan', 'Metode', 'Harga', 'Status'], Product::with('category')->get()->map(fn ($p) => [
            $p->product_code,
            $p->name,
            $p->category?->name,
            $p->service_type,
            $p->print_method,
            $p->base_price,
            $p->is_active ? 'Aktif' : 'Nonaktif',
        ]));
    }

    public function categories(): StreamedResponse
    {
        return $this->csv('categories.csv', ['Kategori', 'Slug', 'Jumlah Produk', 'Status'], Category::withCount('products')->get()->map(fn ($c) => [
            $c->name,
            $c->slug,
            $c->products_count,
            $c->is_active ? 'Aktif' : 'Nonaktif',
        ]));
    }

    public function orders(): StreamedResponse
    {
        return $this->csv('orders.csv', ['Order', 'Customer', 'Total', 'Pembayaran', 'Status', 'Tanggal'], Order::with('user')->get()->map(fn ($o) => [
            $o->order_number,
            $o->user?->name,
            $o->grand_total,
            $o->payment_status,
            $o->status,
            $o->created_at,
        ]));
    }

    public function payments(): StreamedResponse
    {
        return $this->csv('payments.csv', ['Payment', 'Order', 'Nominal', 'Metode', 'Status'], Payment::with('order')->get()->map(fn ($p) => [
            $p->payment_number,
            $p->order?->order_number,
            $p->amount,
            $p->method,
            $p->status,
        ]));
    }

    public function materials(): StreamedResponse
    {
        return $this->csv('materials.csv', ['Bahan', 'Kategori', 'Supplier', 'Stok', 'Minimum', 'Satuan'], Material::with(['category', 'supplier'])->get()->map(fn ($m) => [
            $m->name,
            $m->category?->name,
            $m->supplier?->name,
            $m->current_stock,
            $m->minimum_stock,
            $m->unit,
        ]));
    }

    private function csv(string $filename, array $headers, $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
