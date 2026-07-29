<?php

namespace Tests\Feature\Routing;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ApiRoutesTest extends TestCase
{
    public function test_api_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('api.v1.car-washes.show'));
        $this->assertTrue(Route::has('api.v1.auth.request-otp'));
        $this->assertTrue(Route::has('api.v1.bookings.store'));
    }
}
