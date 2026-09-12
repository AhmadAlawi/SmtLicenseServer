<?php

namespace App\Mail;

use App\Models\Customer;
use App\Models\Release;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/** Sent to every active customer when staff publish a new release — heads-up only, they install it themselves whenever they want. */
class UpdateAvailableMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Customer $customer, public Release $release) {}

    public function build(): self
    {
        $domains = $this->customer->licenses()
            ->where('status', '!=', 'suspended')
            ->with('instance')
            ->get()
            ->pluck('instance.default_domain')
            ->filter()
            ->values();

        return $this->subject("Update available: version {$this->release->version}")
            ->view('emails.update-available', ['domains' => $domains]);
    }
}
