<?php

namespace App\Http\Controllers;

use App\Services\CarWashInvitationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function show(
        string $token,
        Request $request,
        CarWashInvitationService $service,
    ): View {
        $invitation = $service->findUsable($token, $request->user());

        return view('invitations.show', compact('invitation', 'token'));
    }

    public function accept(
        string $token,
        Request $request,
        CarWashInvitationService $service,
    ): RedirectResponse {
        $invitation = $service->accept($token, $request->user());

        return redirect()
            ->route('carwash.dashboard', $invitation->carWash)
            ->with('success', 'دعوت‌نامه پذیرفته شد.');
    }
}
