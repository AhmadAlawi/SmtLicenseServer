<?php

namespace App\Http\Controllers\Dashboard;

use App\Actions\Releases\PublishRelease;
use App\Http\Controllers\Controller;
use App\Models\Release;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Staff-side release publishing — SmtLicenseServer hosts the feed SaasPOS's own self-update engine polls (see UpdateFeedController). */
class ReleaseController extends Controller
{
    public function index(): View
    {
        $releases = Release::query()->with('publisher')->latest('published_at')->paginate(25);

        return view('dashboard.releases.index', compact('releases'));
    }

    public function create(): View
    {
        return view('dashboard.releases.create');
    }

    public function store(Request $request, PublishRelease $publish): RedirectResponse
    {
        $data = $request->validate([
            'version'   => ['required', 'string', 'max:32', Rule::unique('releases', 'version')],
            'channel'   => ['required', 'string', 'in:stable,beta'],
            'changelog' => ['nullable', 'string', 'max:5000'],
            'zip'       => ['required', 'file', 'mimes:zip', 'max:512000'],
        ]);

        try {
            $release = $publish($request->file('zip'), $data['version'], $data['channel'], $data['changelog'] ?? null, $request->user()->id);
        } catch (\Throwable $e) {
            return back()->withErrors(['zip' => $e->getMessage()])->withInput();
        }

        return redirect()->route('dashboard.releases.index')
            ->with('status', "Version {$release->version} published on the {$release->channel} channel — every active customer has been emailed.");
    }
}
