<?php

namespace App\Jobs;

use App\Utils\SMS;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected String $phoneNumber;
    protected String $message;

    /**
     * Create a new job instance.
     */
    public function __construct(String $phoneNumber, String $message)
    {
        $this->phoneNumber = $phoneNumber;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        SMS::send($this->phoneNumber, $this->message);
    }
}
