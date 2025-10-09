<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $user;
    public $expiration;

    public function __construct($otp, $user, $expiration = 10)
    {
        $this->otp = $otp;
        $this->user = $user;
        $this->expiration = $expiration;
    }

    public function build()
    {
        return $this->subject('Your Password Reset OTP')
            ->view('Admin.Mail.password-reset-otp')
            ->with([
                'otp' => $this->otp,
                'user' => $this->user,
                'expiration' => $this->expiration,
            ]);
    }
}
