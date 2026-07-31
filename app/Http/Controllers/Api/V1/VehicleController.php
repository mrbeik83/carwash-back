<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreVehicleRequest;
use App\Http\Resources\Api\V1\VehicleResource;
use App\Models\UserVehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VehicleController extends Controller
{
    public function index(): JsonResponse
    {
        return VehicleResource::collection(
            request()->user()->vehicles()->with('vehicleType')->latest()->get(),
        )->response();
    }

    public function store(StoreVehicleRequest $request): JsonResponse
    {
        $vehicle = DB::transaction(function () use ($request): UserVehicle {
            if ($request->boolean('is_default')) {
                $request->user()->vehicles()->update(['is_default' => false]);
            }

            return $request->user()->vehicles()->create([
                ...$request->validated(),
                'plate_number_normalized' => Str::of($request->input('plate_number', ''))->replace(' ', '')->lower()->toString() ?: null,
            ]);
        });

        return (new VehicleResource($vehicle->load('vehicleType')))->response()->setStatusCode(201);
    }

    public function destroy(UserVehicle $vehicle): JsonResponse
    {
        abort_unless($vehicle->user_id === request()->user()->getKey(), 404);
        $vehicle->delete();
        return response()->json(status: 204);
    }
}
