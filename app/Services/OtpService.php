<?php

namespace App\Services;

use App\Models\User;

class OtpService
{
    /**
     * OTP expiry in minutes.
     */
    public const TTL = 5;

    /**
     * Generate a 6-digit OTP, persist it to the user record and send via SMS.
     * Returns true on success, false if SMS delivery failed.
     */
    public function send(User $user, SmsService $sms): bool
    {
        $code = $this->generateCode();

        $user->update([
            'otp_code'       => $code,
            'otp_expires_at' => now()->addMinutes(self::TTL),
        ]);

        $message = "Your Kiddo's Heaven verification code is: {$code}. Valid for " . self::TTL . " minutes.";

        return $sms->send($user->phone, $message);
    }

    /**
     * Verify the OTP submitted by the user.
     */
    public function verify(User $user, string $code): bool
    {
        if (! $user->otp_code || ! $user->otp_expires_at) {
            return false;
        }

        if (now()->isAfter($user->otp_expires_at)) {
            return false; // expired
        }

        if ($user->otp_code !== $code) {
            return false;
        }

        // Clear OTP after successful verification
        $user->update([
            'otp_code'          => null,
            'otp_expires_at'    => null,
            'phone_verified_at' => now(),
        ]);

        return true;
    }

    /**
     * Generate a random 6-digit numeric code.
     */
    protected function generateCode(): string
    {
        return str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }
}
