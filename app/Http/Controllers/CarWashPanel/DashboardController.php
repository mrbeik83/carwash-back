<?php

namespace App\Http\Controllers\CarWashPanel;

use App\Http\Controllers\Controller;
use App\Models\CarWash;
use App\Services\CarWashDashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(CarWash $carWash, CarWashDashboardService $service): View
    {
        return view('carwash.dashboard', [
            'carWash' => $carWash,
            'summary' => $service->summary($carWash),
        ]);
    }
}
