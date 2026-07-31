<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vehicle_type_id' => $this->vehicle_type_id,
            'vehicle_type' => $this->whenLoaded('vehicleType', fn (): ?array => $this->vehicleType ? [
                'id' => $this->vehicleType->id,
                'name' => $this->vehicleType->name,
                'slug' => $this->vehicleType->slug,
                'size_class' => $this->vehicleType->size_class,
            ] : null),
            'plate_number' => $this->plate_number,
            'brand' => $this->brand,
            'model' => $this->model,
            'color' => $this->color,
            'production_year' => $this->production_year,
            'nickname' => $this->nickname,
            'is_default' => (bool) $this->is_default,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
