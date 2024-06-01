<?php

namespace App\Jobs;

use App\Mail\VerifyAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $user;
    protected $token;
    /**
     * Create a new job instance.
     */
    public function __construct($user, $token)
    {

        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $email = new VerifyAccount($this->user->name, $this->token);
        Mail::to($this->user->email)->send($email);
    }
}
