<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Multi-driver SMS service.
 *
 * Supported drivers (set SMS_DRIVER in .env):
 *  - vonage   (formerly Nexmo) — easiest free trial, sign up with email only at vonage.com
 *  - twilio   — requires phone verification on signup
 *  - log      — dev/test: prints OTP to laravel.log instead of sending
 *
 * Default: 'log' (safe fallback when no credentials are set)
 */
class SmsService
{
    protected string $driver;

    public function __construct()
    {
        $this->driver = config('services.sms.driver', 'log');
    }

    // ─────────────────────────────────────────────────────────────
    // Public API
    // ─────────────────────────────────────────────────────────────

    public function send(string $to, string $message): bool
    {
        $to = $this->normalisePhone($to);

        return match ($this->driver) {
            'vonage'  => $this->sendViaVonage($to, $message),
            'twilio'  => $this->sendViaTwilio($to, $message),
            default   => $this->sendViaLog($to, $message),
        };
    }

    // ─────────────────────────────────────────────────────────────
    // Drivers
    // ─────────────────────────────────────────────────────────────

    /**
     * Vonage (Nexmo) — free trial at https://www.vonage.com
     * Sign up with email only (no phone verification required).
     * Free €2 credit ≈ 20–40 SMS messages.
     *
     * Required .env:
     *   SMS_DRIVER=vonage
     *   VONAGE_KEY=xxxxxxxx
     *   VONAGE_SECRET=xxxxxxxxxxxxxxxx
     *   VONAGE_FROM=KiddosH       ← alphanumeric sender ID (max 11 chars, or use a number)
     */
    protected function sendViaVonage(string $to, string $message): bool
    {
        $key    = config('services.vonage.key');
        $secret = config('services.vonage.secret');
        $from   = config('services.vonage.from', config('app.name'));

        if (! $key || ! $secret) {
            Log::warning('SmsService[vonage]: credentials not set — falling back to log.', ['to' => $to]);
            return $this->sendViaLog($to, $message);
        }

        try {
            $response = Http::asForm()->post('https://rest.nexmo.com/sms/json', [
                'api_key'    => $key,
                'api_secret' => $secret,
                'to'         => $to,
                'from'       => $from,
                'text'       => $message,
            ]);

            $body    = $response->json();
            $status  = $body['messages'][0]['status'] ?? '1';

            if ($status === '0') {
                Log::info('SmsService[vonage]: SMS sent', ['to' => $to]);
                return true;
            }

            $errText = $body['messages'][0]['error-text'] ?? 'Unknown error';
            Log::error('SmsService[vonage]: SMS failed', ['to' => $to, 'error' => $errText, 'status' => $status]);
            return false;

        } catch (\Exception $e) {
            Log::error('SmsService[vonage]: exception', ['to' => $to, 'error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Twilio — https://www.twilio.com
     * Requires the twilio/sdk composer package.
     *
     * Required .env:
     *   SMS_DRIVER=twilio
     *   TWILIO_SID=ACxxxxxxxxxx
     *   TWILIO_TOKEN=xxxxxxxxxxxxxx
     *   TWILIO_FROM=+1234567890
     */
    protected function sendViaTwilio(string $to, string $message): bool
    {
        if (! class_exists(\Twilio\Rest\Client::class)) {
            Log::error('SmsService[twilio]: twilio/sdk not installed. Run: composer require twilio/sdk');
            return false;
        }

        $sid   = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from  = config('services.twilio.from');

        if (! $sid || ! $token) {
            Log::warning('SmsService[twilio]: credentials not set — falling back to log.', ['to' => $to]);
            return $this->sendViaLog($to, $message);
        }

        try {
            $client = new \Twilio\Rest\Client($sid, $token);
            $client->messages->create($to, ['from' => $from, 'body' => $message]);
            Log::info('SmsService[twilio]: SMS sent', ['to' => $to]);
            return true;
        } catch (\Exception $e) {
            Log::error('SmsService[twilio]: exception', ['to' => $to, 'error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Log driver — dev/test fallback.
     * OTP is written to storage/logs/laravel.log and also cached under the key
     * "otp_debug:{phone}" so you can retrieve it via artisan tinker if needed.
     */
    protected function sendViaLog(string $to, string $message): bool
    {
        Log::channel('stack')->info('──────────────────────────────────────────');
        Log::channel('stack')->info('SmsService [LOG DRIVER] — OTP not sent via SMS');
        Log::channel('stack')->info("  To      : {$to}");
        Log::channel('stack')->info("  Message : {$message}");
        Log::channel('stack')->info('──────────────────────────────────────────');

        // Store in cache for easy retrieval during dev (10 min TTL)
        cache()->put('otp_debug:' . $to, $message, now()->addMinutes(10));

        return true;
    }

    // ─────────────────────────────────────────────────────────────
    // Phone number normalisation
    // ─────────────────────────────────────────────────────────────

    protected function normalisePhone(string $phone): string
    {
        $phone = preg_replace('/[\s\-().]/', '', $phone);

        if (str_starts_with($phone, '+')) {
            return $phone;
        }

        // 01XXXXXXXXX (BD local, 11 digits) → +88001XXXXXXXXX
        if (preg_match('/^01[3-9]\d{8}$/', $phone)) {
            return '+880' . $phone;
        }

        // 8801XXXXXXXXX (without leading +)
        if (str_starts_with($phone, '880') && strlen($phone) === 13) {
            return '+' . $phone;
        }

        return $phone;
    }
}

