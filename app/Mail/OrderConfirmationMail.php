<?php

namespace App\Mail;

use App\Models\Customer;
use App\Models\Plan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/** Sent the moment Stripe confirms payment (checkout.session.completed) — before provisioning finishes, so the customer knows the charge went through immediately. */
class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Customer $customer, public Plan $plan, public string $subdomainSlug, public string $rootDomain) {}

    public function build(): self
    {
        return $this->subject('Your subscription is confirmed')
            ->view('emails.order-confirmation');
    }
}
