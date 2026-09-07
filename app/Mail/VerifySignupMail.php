<?php

namespace App\Mail;

use App\Models\PendingSignup;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/** Sent right after the /pricing form is submitted, before any Stripe interaction — the link IS what starts checkout. */
class VerifySignupMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PendingSignup $signup, public string $verifyUrl) {}

    public function build(): self
    {
        return $this->subject('Confirm your email to continue')
            ->view('emails.verify-signup');
    }
}
