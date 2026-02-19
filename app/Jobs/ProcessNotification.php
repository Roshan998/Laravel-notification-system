<?php

namespace App\Jobs;

use App\Models\Notification;      
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\GenericNotificationMail;

class ProcessNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $backoff = [10, 30, 120]; 

    public function __construct(public Notification $notification) { }

    public function handle()
    {
        try {
            Log::info("Processing notification {$this->notification->id}");

            if ($this->notification->type === 'email') {
                $user = $this->notification->user;

                if (!$user) {
                    throw new \Exception('User not found for notification');
                }

                Mail::to($user->email)
                    ->send(new GenericNotificationMail($this->notification));
            }

            $this->notification->update([
                'status' => 'sent',
                'processed_at' => now()
            ]);

            Log::info("Notification {$this->notification->id} sent successfully");
        } catch (\Throwable $e) {
            $this->notification->increment('attempts');
            $this->notification->update([
                'status' => 'failed',
                'last_error' => $e->getMessage()
            ]);

            Log::error("Notification {$this->notification->id} failed: {$e->getMessage()}");

            throw $e; // triggers retry with backoff
        }
    }
}
