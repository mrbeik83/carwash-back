<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\CarWash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'status' => ['nullable', Rule::enum(BookingStatus::class)],
            'car_wash_id' => ['nullable', 'integer', 'exists:car_washes,id'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $bookings = Booking::query()
            ->with(['carWash', 'customer', 'slot'])
            ->when($request->filled('status'), fn ($query) =>
                $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('car_wash_id'), fn ($query) =>
                $query->where('car_wash_id', $request->integer('car_wash_id')))
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = '%'.$request->string('q')->toString().'%';
                $query->where(fn ($inner) => $inner
                    ->where('tracking_code', 'like', $term)
                    ->orWhere('customer_name', 'like', $term)
                    ->orWhere('customer_mobile', 'like', $term));
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.bookings.index', [
            'bookings' => $bookings,
            'carWashes' => CarWash::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
