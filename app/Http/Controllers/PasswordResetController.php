<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    public function request()
    {
        return view('auth.forgot-password');
    }

    public function email(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);
        $this->applyMailSettings();
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function reset(string $token, Request $request)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function ($user, $password) {
            $user->forceFill(['password' => bcrypt($password)])->save();
        });

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    private function applyMailSettings(): void
    {
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => Setting::valueOf('smtp_host', config('mail.mailers.smtp.host')),
            'mail.mailers.smtp.port' => (int) Setting::valueOf('smtp_port', config('mail.mailers.smtp.port')),
            'mail.mailers.smtp.username' => Setting::valueOf('smtp_username', config('mail.mailers.smtp.username')),
            'mail.mailers.smtp.password' => Setting::valueOf('smtp_password', config('mail.mailers.smtp.password')),
            'mail.mailers.smtp.encryption' => Setting::valueOf('smtp_encryption') === 'none' ? null : Setting::valueOf('smtp_encryption', config('mail.mailers.smtp.encryption')),
            'mail.from.address' => Setting::valueOf('smtp_from_address', config('mail.from.address')),
            'mail.from.name' => Setting::valueOf('smtp_from_name', config('mail.from.name')),
        ]);
    }
}
