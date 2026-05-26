<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Services\WahaService;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index', [
            'settings' => Setting::pluck('value', 'key'),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:160'],
            'company_logo' => ['nullable', 'image', 'max:4096'],
            'company_profile' => ['nullable', 'string'],
            'company_phone' => ['nullable', 'string', 'max:60'],
            'company_email' => ['nullable', 'email'],
            'company_website' => ['nullable', 'string', 'max:160'],
            'company_address' => ['nullable', 'string'],
            'receipt_printer_name' => ['nullable', 'string', 'max:120'],
            'receipt_printer_paper' => ['nullable', 'string', 'max:60'],
            'production_printer_name' => ['nullable', 'string', 'max:120'],
            'production_printer_paper' => ['nullable', 'string', 'max:60'],
            'theme_name' => ['required', 'in:professional,green,dark,compact'],
            'header_text' => ['nullable', 'string'],
            'footer_text' => ['nullable', 'string'],
            'waha_enabled' => ['nullable', 'boolean'],
            'waha_base_url' => ['nullable', 'url'],
            'waha_api_key' => ['nullable', 'string', 'max:255'],
            'waha_session' => ['nullable', 'string', 'max:120'],
            'waha_admin_number' => ['nullable', 'string', 'max:60'],
            'waha_notify_admin_order' => ['nullable', 'boolean'],
            'waha_notify_customer_order' => ['nullable', 'boolean'],
            'waha_notify_payment' => ['nullable', 'boolean'],
            'waha_verify_ssl' => ['nullable', 'boolean'],
            'waha_template_order' => ['nullable', 'string'],
            'waha_template_payment' => ['nullable', 'string'],
            'smtp_host' => ['nullable', 'string', 'max:160'],
            'smtp_port' => ['nullable', 'integer'],
            'smtp_username' => ['nullable', 'string', 'max:160'],
            'smtp_password' => ['nullable', 'string', 'max:255'],
            'smtp_encryption' => ['nullable', 'in:tls,ssl,none'],
            'smtp_from_address' => ['nullable', 'email'],
            'smtp_from_name' => ['nullable', 'string', 'max:160'],
            'permissions' => ['nullable', 'array'],
        ]);

        if ($request->hasFile('company_logo')) {
            $data['company_logo'] = $request->file('company_logo')->store('public/company');
        } else {
            unset($data['company_logo']);
        }

        foreach ($data as $key => $value) {
            if ($key === 'permissions') {
                Setting::put('role_permissions', $value, 'permissions', 'json');
                continue;
            }
            $group = str_starts_with($key, 'smtp_') ? 'mail'
                : (str_starts_with($key, 'waha_') ? 'notification'
                : (str_contains($key, 'printer') ? 'printer'
                : (str_contains($key, 'theme') ? 'theme'
                : (str_contains($key, 'header') || str_contains($key, 'footer') ? 'layout' : 'company'))));
            Setting::put($key, $value, $group, is_bool($value) ? 'boolean' : 'string');
        }

        foreach (['waha_enabled', 'waha_notify_admin_order', 'waha_notify_customer_order', 'waha_notify_payment', 'waha_verify_ssl'] as $key) {
            Setting::put($key, $request->boolean($key) ? '1' : '0', 'notification', 'boolean');
        }

        return back()->with('status', 'Setting sistem diperbarui.');
    }

    public function about()
    {
        return view('admin.settings.about');
    }

    public function testWaha(Request $request, WahaService $waha)
    {
        $data = $request->validate([
            'waha_base_url' => ['nullable', 'url'],
            'waha_session' => ['nullable', 'string', 'max:120'],
            'waha_admin_number' => ['nullable', 'string', 'max:60'],
        ]);

        $baseUrl = rtrim($data['waha_base_url'] ?: Setting::valueOf('waha_base_url'), '/');
        $session = $data['waha_session'] ?: Setting::valueOf('waha_session', 'default');
        $number = $data['waha_admin_number'] ?: Setting::valueOf('waha_admin_number');

        if (! $baseUrl || ! $number) {
            return back()->withErrors(['waha' => 'Base URL WAHA dan nomor admin wajib diisi sebelum test.']);
        }

        Setting::put('waha_base_url', $baseUrl, 'notification');
        if ($request->filled('waha_api_key')) {
            Setting::put('waha_api_key', $request->waha_api_key, 'notification');
        }
        Setting::put('waha_session', $session, 'notification');
        Setting::put('waha_admin_number', $number, 'notification');
        Setting::put('waha_enabled', '1', 'notification', 'boolean');

        $log = $waha->sendText($number, 'Test notifikasi WAHA dari GreenPrinting berhasil dikirim pada '.now()->format('d M Y H:i'), $session);
        if ($log->status !== 'sent') {
            return back()->withErrors(['waha' => 'Test WAHA gagal: '.$log->error_message.' '.$log->response_body]);
        }

        return back()->with('status', 'Test WAHA berhasil dikirim ke '.$log->recipient.'.');
    }
}
