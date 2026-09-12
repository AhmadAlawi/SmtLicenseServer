<?php

namespace App\Actions\Releases;

use App\Mail\UpdateAvailableMail;
use App\Models\Customer;
use App\Models\Release;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Publishing a release does three things in one step, so staff never touch
 * a manual signing workflow: (1) store the zip, (2) sign its sha256 with
 * this server's Ed25519 secret key (the same key every tenant already has
 * the public half of), (3) email every active customer that an update is
 * available — they install it themselves, whenever they want, from their
 * own Settings → Updates screen (SaasPOS's existing self-update engine,
 * untouched — this is only what feeds it).
 */
class PublishRelease
{
    public function __invoke(UploadedFile $zip, string $version, string $channel, ?string $changelog, ?int $publishedById): Release
    {
        $secretKeyBase64 = (string) config('services.updates.signing_secret_key', '');
        if ($secretKeyBase64 === '') {
            throw new RuntimeException('UPDATE_SIGNING_SECRET_KEY is not configured — cannot sign a release.');
        }

        $bytes  = (string) file_get_contents($zip->getRealPath());
        $sha256 = hash('sha256', $bytes);

        $secretKey = base64_decode($secretKeyBase64, true);
        if ($secretKey === false || strlen($secretKey) !== SODIUM_CRYPTO_SIGN_SECRETKEYBYTES) {
            throw new RuntimeException('UPDATE_SIGNING_SECRET_KEY is not a valid Ed25519 secret key.');
        }

        // Signed over the raw zip bytes — SaasPOS's SignatureVerifier
        // verifies the same way (bytes, not just the checksum), so the
        // sha256 field is a fast pre-check but the signature is what
        // actually proves authenticity.
        $signature = base64_encode(sodium_crypto_sign_detached($bytes, $secretKey));

        $zipPath = "releases/{$version}.zip";
        Storage::disk('local')->put($zipPath, $bytes);

        $release = Release::create([
            'version'      => $version,
            'channel'      => $channel,
            'changelog'    => $changelog,
            'zip_path'     => $zipPath,
            'sha256'       => $sha256,
            'signature'    => $signature,
            'published_at' => now(),
            'published_by' => $publishedById,
        ]);

        $this->notifyCustomers($release);

        return $release;
    }

    /**
     * Every customer with at least one non-suspended license — a suspended
     * subscription's instance shouldn't be nudged to update (and may not
     * even be running). Queued, not sent inline: this can be dozens/
     * hundreds of emails and the publish action shouldn't wait on SMTP/API
     * latency for each one.
     */
    private function notifyCustomers(Release $release): void
    {
        Customer::query()
            ->whereHas('licenses', fn ($q) => $q->where('status', '!=', 'suspended'))
            ->chunk(100, function ($customers) use ($release) {
                foreach ($customers as $customer) {
                    Mail::to($customer->email)->queue(new UpdateAvailableMail($customer, $release));
                }
            });
    }
}
