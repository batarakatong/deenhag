<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Role;
use Illuminate\Http\Request;

class WahaWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $event = $request->input('event', $request->input('type', 'waha'));
        $payload = $request->all();
        $message = $request->input('payload.body')
            ?? $request->input('body')
            ?? $request->input('message')
            ?? 'Event WAHA diterima.';

        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            foreach ($adminRole->users as $user) {
                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'WAHA: '.$event,
                    'message' => is_string($message) ? $message : json_encode($message),
                    'type' => 'waha',
                    'data' => $payload,
                ]);
            }
        }

        return response()->json(['ok' => true]);
    }
}
