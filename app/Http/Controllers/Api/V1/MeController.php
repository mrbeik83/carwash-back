<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'data' => [
                'id' => $user->public_id,
                'full_name' => $user->full_name,
                'mobile' => $user->mobile,
                'email' => $user->email,
                'mobile_verified_at' => $user->mobile_verified_at,
            ],
        ]);
    }
}
