<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\OrderFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FilePreviewController extends Controller
{
    public function media(string $path)
    {
        $path = ltrim($path, '/');
        $publicPath = str_starts_with($path, 'public/') ? substr($path, 7) : $path;

        if (Storage::disk('public')->exists($publicPath)) {
            return response()->file(Storage::disk('public')->path($publicPath));
        }

        if (Storage::exists($path)) {
            return response()->file(Storage::path($path));
        }

        if (! str_starts_with($path, 'public/') && Storage::exists('public/'.$path)) {
            return response()->file(Storage::path('public/'.$path));
        }

        abort(404);
    }

    public function cartDesign(CartItem $item)
    {
        abort_unless($item->cart->user_id === auth()->id(), 403);
        abort_unless($item->design_file_path && Storage::exists($item->design_file_path), 404);

        return response()->file(Storage::path($item->design_file_path), [
            'Content-Disposition' => 'inline; filename="'.$item->design_file_name.'"',
        ]);
    }

    public function orderFile(OrderFile $file)
    {
        $user = auth()->user();
        $file->load('order');
        $allowed = $user->hasRole('admin', 'staff') || $file->order->user_id === $user->id;
        abort_unless($allowed, 403);
        abort_unless(Storage::exists($file->file_path), 404);

        return response()->file(Storage::path($file->file_path), [
            'Content-Disposition' => 'inline; filename="'.$file->original_name.'"',
        ]);
    }

    public function publicOrderFile(Request $request, OrderFile $file)
    {
        $file->load('order');
        abort_unless($request->query('order_number') === $file->order->order_number, 403);
        abort_unless(Storage::exists($file->file_path), 404);

        return response()->file(Storage::path($file->file_path), [
            'Content-Disposition' => 'inline; filename="'.$file->original_name.'"',
        ]);
    }
}
