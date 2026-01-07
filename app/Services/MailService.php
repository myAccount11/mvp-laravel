<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use App\Jobs\SendMailJob;

class MailService
{
    public function sendMailCreatePassword($to, $name, $token)
    {
        $frontEndUrl = config('app.frontend_url');
        $createPasswordURL = "{$frontEndUrl}/create-password?token={$token}&email={$to}";

//        Queue::push(new SendMailJob([
//            'to' => $to,
//            'subject' => 'Set Up Your New Password',
//            'template' => 'create-password',
//            'context' => [
//                'name' => $name,
//                'createPasswordURL' => $createPasswordURL,
//            ],
//        ]));
    }

    public function sendMailVerifyEmail($to, $name, $token)
    {
        $frontEndUrl = config('app.frontend_url');
        $loginURL = "{$frontEndUrl}/login?token={$token}";

//        Queue::push(new SendMailJob([
//            'to' => $to,
//            'subject' => 'Verify Your Account for MVP App',
//            'template' => 'verify-email',
//            'context' => [
//                'name' => $name,
//                'loginURL' => $loginURL,
//            ],
//        ]));
    }

    public function sendMailResetPassword($to, $name, $token)
    {
        $frontEndUrl = config('app.frontend_url');
        $resetPasswordURL = "{$frontEndUrl}/reset-password?token={$token}&email={$to}";

//        Queue::push(new SendMailJob([
//            'to' => $to,
//            'subject' => 'Password Reset Request for MVP App',
//            'template' => 'reset-password',
//            'context' => [
//                'name' => $name,
//                'resetPasswordURL' => $resetPasswordURL,
//            ],
//        ]));
    }

    public function sendMailCongretsCoach($to, $name, $coachlevel, $start, $end)
    {
//        Queue::push(new SendMailJob([
//            'to' => $to,
//            'subject' => 'Congratulations on Your Coaching License Renewal!',
//            'template' => 'congrets-coach',
//            'context' => [
//                'name' => $name ?: 'Coach',
//                'coachlevel' => $coachlevel,
//                'start' => $start,
//                'end' => $end,
//            ],
//        ]));
    }
}

