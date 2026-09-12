<?php

namespace App\Http\Controllers;

use App\Models\Release;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * The public feed every SaasPOS tenant's own updater engine polls
 * (SaasPOS app/Services/Updater/UpdateClient.php + CheckForUpdate.php,
 * both pre-existing and unchanged — this controller is the only new
 * piece, what actually feeds them). No auth: the feed and the zips it
 * points at are meant to be fetched by every tenant's own server, which
 * has no credential to this app beyond its per-instance license key
 * (used only for the separate /api/v1/instances/validate phone-home, not
 * for updates).
 */
class UpdateFeedController extends Controller
{
    public function feed(): JsonResponse
    {
        $releases = Release::query()->whereNotNull('published_at')->get();

        $channels = [];
        $out      = [];

        foreach ($releases->groupBy('channel') as $channel => $group) {
            $latest = $group->sortByDesc('published_at')->first();
            $channels[$channel] = ['latest_version' => $latest->version];
        }

        foreach ($releases as $release) {
            $out[$release->version] = [
                'version'      => $release->version,
                'download_url' => route('updates.download', $release),
                'signature'    => $release->signature,
                'sha256'       => $release->sha256,
                'changelog'    => $release->changelog,
            ];
        }

        return response()->json(['channels' => $channels, 'releases' => $out]);
    }

    public function download(Release $release): Response
    {
        abort_unless(Storage::disk('local')->exists($release->zip_path), 404);

        return Storage::disk('local')->download($release->zip_path, "pos-{$release->version}.zip");
    }
}
