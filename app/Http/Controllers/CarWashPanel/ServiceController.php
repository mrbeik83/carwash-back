<?php

namespace App\Http\Controllers\CarWashPanel;

use App\Http\Controllers\Controller;
use App\Http\Requests\CarWashPanel\StoreServiceRequest;
use App\Models\CarWash;
use App\Models\CarWashService;
use App\Models\VehicleType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(CarWash $carWash): View
    {
        return view('carwash.services.index', [
            'carWash' => $carWash,
            'services' => $carWash->services()->with('vehiclePrices.vehicleType')->orderBy('sort_order')->paginate(25),
            'vehicleTypes' => VehicleType::query()->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function store(StoreServiceRequest $request, CarWash $carWash): RedirectResponse
    {
        DB::transaction(function () use ($request, $carWash): void {
            $payload = $request->safe()->except('prices');
            $service = $carWash->services()->create($payload);
            $service->vehiclePrices()->createMany($request->validated('prices'));
        });

        return back()->with('success', 'خدمت ایجاد شد.');
    }

    public function update(
        StoreServiceRequest $request,
        CarWash $carWash,
        CarWashService $service,
    ): RedirectResponse {
        abort_unless($service->car_wash_id === $carWash->getKey(), 404);

        DB::transaction(function () use ($request, $service): void {
            $service->update($request->safe()->except('prices'));
            foreach ($request->validated('prices') as $price) {
                $service->vehiclePrices()->updateOrCreate(
                    ['vehicle_type_id' => $price['vehicle_type_id']],
                    $price,
                );
            }
        });

        return back()->with('success', 'خدمت به‌روزرسانی شد.');
    }

    public function destroy(CarWash $carWash, CarWashService $service): RedirectResponse
    {
        abort_unless($service->car_wash_id === $carWash->getKey(), 404);
        $service->delete();
        return back()->with('success', 'خدمت غیرفعال شد.');
    }
}
