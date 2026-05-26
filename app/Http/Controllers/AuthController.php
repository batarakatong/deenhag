<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password tidak sesuai.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        if (auth()->user()->two_factor_enabled) {
            $code = (string) random_int(100000, 999999);
            auth()->user()->update([
                'two_factor_code' => Hash::make($code),
                'two_factor_expires_at' => now()->addMinutes(10),
            ]);
            try {
                Mail::raw('Kode 2FA GreenPrinting Anda: '.$code, fn ($mail) => $mail->to(auth()->user()->email)->subject('Kode 2FA GreenPrinting'));
            } catch (\Throwable) {
                // If SMTP is not ready, the challenge still shows and admin can disable/reset from backend.
            }
            $request->session()->put('2fa:user:id', auth()->id());
            Auth::logout();

            return redirect()->route('2fa.challenge');
        }

        return auth()->user()->hasRole('admin', 'staff')
            ? redirect()->route('admin.dashboard')
            : redirect()->route('customer.dashboard');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        $role = Role::where('name', 'customer')->firstOrFail();
        $user = User::create([
            'role_id' => $role->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        Customer::create([
            'user_id' => $user->id,
            'customer_code' => 'CUST-'.str_pad((string) $user->id, 5, '0', STR_PAD_LEFT),
        ]);

        Auth::login($user);

        return redirect()->route('customer.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function challenge()
    {
        return view('auth.two-factor');
    }

    public function verifyTwoFactor(Request $request)
    {
        $request->validate(['code' => ['required', 'digits:6']]);
        $user = User::findOrFail($request->session()->get('2fa:user:id'));
        if (! $user->two_factor_expires_at || now()->gt($user->two_factor_expires_at) || ! Hash::check($request->code, $user->two_factor_code)) {
            return back()->withErrors(['code' => 'Kode 2FA tidak valid atau sudah kedaluwarsa.']);
        }
        $user->update(['two_factor_code' => null, 'two_factor_expires_at' => null]);
        Auth::login($user);
        $request->session()->forget('2fa:user:id');
        $request->session()->regenerate();

        return $user->hasRole('admin', 'staff') ? redirect()->route('admin.dashboard') : redirect()->route('customer.dashboard');
    }
}
