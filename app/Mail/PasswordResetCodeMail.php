<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $code,
        public readonly string $expiresInHuman
    ) {
    }

    public function build(): self
    {
        return $this->subject('Password Reset Verification Code')
            ->markdown('emails.reset-code', [
                'code' => $this->code,
                'expiresInHuman' => $this->expiresInHuman,
            ]);
    }
}
