<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())->latest()->paginate(20);
        Notification::where('user_id', auth()->id())->where('is_read', false)->update(['is_read' => true, 'read_at' => now()]);

        return view('admin.notifications.index', compact('notifications'));
    }
}
