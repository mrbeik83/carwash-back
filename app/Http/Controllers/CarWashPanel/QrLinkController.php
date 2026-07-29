<?php

namespace App\Http\Controllers\CarWashPanel;

use App\Enums\QrLinkType;
use App\Http\Controllers\Controller;
use App\Models\CarWash;
use App\Models\QrLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class QrLinkController extends Controller
{
    public function index(CarWash $carWash): View
    {
        return view('carwash.qr.index', [
            'carWash' => $carWash,
            'links' => $carWash->qrLinks()->withCount('scans')->latest()->paginate(30),
        ]);
    }

    public function store(Request $request, CarWash $carWash): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'type' => ['required', Rule::enum(QrLinkType::class)],
            'campaign' => ['nullable', 'string', 'max:150'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        $carWash->qrLinks()->create([
            ...$data,
            'type' => QrLinkType::from($data['type']),
            'token' => Str::random(48),
            'is_active' => true,
            'created_by' => $request->user()->getKey(),
        ]);

        return back()->with('success', 'لینک QR ساخته شد.');
    }

    public function destroy(CarWash $carWash, QrLink $qrLink): RedirectResponse
    {
        abort_unless($qrLink->car_wash_id === $carWash->getKey(), 404);
        $qrLink->update(['is_active' => false]);
        return back()->with('success', 'لینک QR غیرفعال شد.');
    }
}
