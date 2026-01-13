<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmail;
use App\Mail\CreatePassword;
use App\Mail\ResetPassword;

class MailService
{
    /**
     * Send email for creating password
     */
    public function sendMailCreatePassword(string $to, string $name, string $token): void
    {
        Mail::to($to)->send(new CreatePassword($name, $token, $to));
    }

    /**
     * Send email verification
     */
    public function sendMailVerifyEmail(string $to, string $name, string $token): void
    {
        Mail::to($to)->send(new VerifyEmail($name, $token));
    }

    /**
     * Send password reset email
     */
    public function sendMailResetPassword(string $to, string $name, string $token): void
    {
        Mail::to($to)->send(new ResetPassword($name, $token, $to));
    }

    /**
     * Send congratulations email to coach
     */
    public function sendMailCongretsCoach(string $to, string $name, string $coachlevel, string $start, string $end): void
    {
        // TODO: Create CongratsCoach mail class
    }
}

