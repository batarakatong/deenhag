<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\WahaMessageLog;
use Illuminate\Support\Facades\Http;

class WahaService
{
    public function sendText(string $recipient, string $message, ?string $session = null): WahaMessageLog
    {
        $recipient = $this->normalizeNumber($recipient);
        $session = $session ?: Setting::valueOf('waha_session', 'default');

        $log = WahaMessageLog::create([
            'recipient' => $recipient,
            'session' => $session,
            'message' => $message,
            'status' => 'pending',
        ]);

        return $this->sendLog($log);
    }

    public function sendLog(WahaMessageLog $log): WahaMessageLog
    {
        $baseUrl = rtrim((string) Setting::valueOf('waha_base_url'), '/');
        if (! $baseUrl || Setting::valueOf('waha_enabled', '0') !== '1') {
            $log->update([
                'status' => 'skipped',
                'error_message' => 'WAHA belum aktif atau base URL kosong.',
                'last_attempt_at' => now(),
            ]);

            return $log;
        }

        try {
            $client = Http::timeout(15);
            if (Setting::valueOf('waha_verify_ssl', '0') !== '1') {
                $client = $client->withoutVerifying();
            }

            $apiKey = Setting::valueOf('waha_api_key');
            if ($apiKey) {
                $client = $client->withHeaders(['X-Api-Key' => $apiKey]);
            }

            $response = $client->post($baseUrl.'/api/sendText', [
                'session' => $log->session,
                'chatId' => $log->recipient.'@c.us',
                'text' => $log->message,
            ]);

            $log->update([
                'attempts' => $log->attempts + 1,
                'last_attempt_at' => now(),
                'response_body' => $response->body(),
                'status' => $response->successful() ? 'sent' : 'failed',
                'sent_at' => $response->successful() ? now() : null,
                'error_message' => $response->successful() ? null : 'HTTP '.$response->status(),
            ]);
        } catch (\Throwable $exception) {
            $log->update([
                'attempts' => $log->attempts + 1,
                'last_attempt_at' => now(),
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
            ]);
        }

        return $log;
    }

    public function normalizeNumber(string $number): string
    {
        $number = preg_replace('/\D+/', '', $number);
        if (str_starts_with($number, '0')) {
            return '62'.substr($number, 1);
        }

        return $number;
    }
}
