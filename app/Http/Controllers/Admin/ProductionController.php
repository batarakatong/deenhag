<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Notification;
use App\Models\ProductionStep;
use App\Models\Setting;
use App\Services\StockService;
use App\Services\WahaService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductionController extends Controller
{
    public function index()
    {
        $steps = ProductionStep::where('is_active', true)->orderBy('sort_order')->get();
        $orders = Order::with(['user', 'items'])->whereNotIn('status', ['completed', 'cancelled'])->latest()->get();

        return view('admin.production.index', compact('steps', 'orders'));
    }

    public function updateOrder(Request $request, Order $order, StockService $stock, WahaService $waha)
    {
        $data = $request->validate(['status' => ['required', 'exists:production_steps,status_key']]);
        $old = $order->status;
        $order->update(['status' => $data['status']]);
        if ($old !== 'printing' && $data['status'] === 'printing') {
            $stock->reduceForOrder($order->load('items'));
        }
        if ($old !== $data['status']) {
            $stepName = ProductionStep::where('status_key', $data['status'])->value('name') ?: $data['status'];
            Notification::create([
                'user_id' => $order->user_id,
                'title' => 'Update produksi',
                'message' => 'Pesanan '.$order->order_number.' masuk tahap '.$stepName,
                'type' => 'order',
                'data' => ['order_id' => $order->id, 'status' => $data['status']],
            ]);
            if (Setting::valueOf('waha_notify_customer_order', '0') === '1' && $order->user?->phone) {
                $waha->sendText($order->user->phone, 'Halo '.$order->user->name.', pesanan '.$order->order_number.' masuk tahap '.$stepName.'.');
            }
        }

        return back()->with('status', 'Status produksi diperbarui.');
    }

    public function steps()
    {
        return view('admin.production.steps', [
            'steps' => ProductionStep::orderBy('sort_order')->get(),
        ]);
    }

    public function storeStep(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['required', 'integer'],
        ]);
        ProductionStep::create($data + [
            'status_key' => Str::slug($data['name'], '_'),
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);

        return back()->with('status', 'Tahap produksi ditambahkan.');
    }

    public function updateStep(Request $request, ProductionStep $step)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['required', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['status_key'] = Str::slug($data['name'], '_');
        $data['is_active'] = $request->boolean('is_active');
        $step->update($data);

        return back()->with('status', 'Tahap produksi diperbarui.');
    }

    public function destroyStep(ProductionStep $step)
    {
        $step->delete();

        return back()->with('status', 'Tahap produksi dihapus.');
    }
}
