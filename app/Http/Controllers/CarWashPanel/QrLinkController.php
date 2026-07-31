<?php

namespace App\Http\Controllers\CarWashPanel;

use App\Enums\QrLinkType;
use App\Http\Controllers\Controller;
use App\Models\CarWash;
use App\Models\QrLink;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

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
            'expires_at_date' => ['nullable', 'date'],
            'expires_at_time' => ['nullable', 'required_with:expires_at_date', 'date_format:H:i'],
        ]);

        $expiresAt = null;
        if (! empty($data['expires_at_date'])) {
            $expiresAt = CarbonImmutable::parse(
                $data['expires_at_date'].' '.($data['expires_at_time'] ?? '23:59'),
                $carWash->timezone ?: 'Asia/Tehran',
            );

            if ($expiresAt->isPast()) {
                throw ValidationException::withMessages([
                    'expires_at_date' => 'تاریخ انقضا باید در آینده باشد.',
                ]);
            }
        }

        $carWash->qrLinks()->create([
            'title' => $data['title'],
            'type' => QrLinkType::from($data['type']),
            'campaign' => $data['campaign'] ?? null,
            'expires_at' => $expiresAt?->setTimezone('UTC'),
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
