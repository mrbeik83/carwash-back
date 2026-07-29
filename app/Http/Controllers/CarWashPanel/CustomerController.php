<?php

namespace App\Http\Controllers\CarWashPanel;

use App\Http\Controllers\Controller;
use App\Models\CarWash;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request, CarWash $carWash): View
    {
        $customerIds = $carWash->bookings()
            ->whereNotNull('customer_user_id')
            ->select('customer_user_id')
            ->distinct();

        $customers = User::query()
            ->whereIn('id', $customerIds)
            ->when($request->filled('q'), function ($q) use ($request): void {
                $term = '%'.$request->string('q').'%';
                $q->where(fn ($inner) => $inner
                    ->where('full_name', 'like', $term)
                    ->orWhere('mobile', 'like', $term));
            })
            ->paginate(30)
            ->withQueryString();

        return view('carwash.customers.index', compact('carWash', 'customers'));
    }
}
