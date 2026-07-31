{{-- Compatibility layout. New pages must explicitly extend layouts.admin or layouts.carwash. --}}
@extends(isset($carWash) ? 'layouts.carwash' : 'layouts.admin')
