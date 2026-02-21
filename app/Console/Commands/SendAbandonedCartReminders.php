<?php

namespace App\Console\Commands;

use App\Domains\Marketing\Models\AbandonedCart;
use App\Mail\AbandonedCartReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Scans active abandoned carts and sends timed recovery reminders.
 *
 * Schedule at: ->everyFifteenMinutes()
 *
 * Reminder windows:
 *   1st: 1h after abandonment
 *   2nd: 24h
 *   3rd: 72h
 */
class SendAbandonedCartReminders extends Command
{
    protected $signature   = 'carts:send-reminders {--dry-run : Preview without sending}';
    protected $description = 'Send abandoned cart recovery emails (3-stage)';

    public function handle(): int
    {
        $now  = now();
        $sent = 0;

        foreach ([1, 2, 3] as $reminder) {
            $carts = AbandonedCart::needsReminder($reminder)
                ->whereNotNull('email')
                ->limit(100)
                ->get();

            foreach ($carts as $cart) {
                if ($this->option('dry-run')) {
                    $this->line("DRY-RUN: Reminder #{$reminder} → {$cart->email} ({$cart->token})");
                    continue;
                }

                try {
                    Mail::to($cart->email)->queue(new AbandonedCartReminder($cart, $reminder));
                    $cart->update(["reminder_{$reminder}_sent_at" => $now]);
                    $sent++;
                } catch (\Throwable $e) {
                    $this->error("Failed for cart #{$cart->id}: " . $e->getMessage());
                }
            }
        }

        $this->info("Sent {$sent} reminder(s).");
        return self::SUCCESS;
    }
}
