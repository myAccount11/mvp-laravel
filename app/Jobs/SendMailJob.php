<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected array $mailData)
    {
    }

    public function handle()
    {
        Mail::send(
            "emails.{$this->mailData['template']}",
            $this->mailData['context'],
            function ($message) {
                $message->to($this->mailData['to'])
                    ->subject($this->mailData['subject']);
            }
        );
    }
}

