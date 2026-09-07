<?php

namespace App\Mail;

use App\Models\Instance;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/** Sent once PollRailwayDeployments flips an instance to 'ready'. Never repeats the admin password — the customer set it themselves at signup. */
class InstanceReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Instance $instance, public string $adminEmail) {}

    public function build(): self
    {
        return $this->subject('Your POS instance is ready')
            ->view('emails.instance-ready');
    }
}
