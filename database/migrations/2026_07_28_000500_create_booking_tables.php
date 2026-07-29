<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();
            $table->string('tracking_code', 30)->unique();

            $table->foreignId('car_wash_id')->constrained()->restrictOnDelete();
            $table->foreignId('customer_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('user_vehicles')->nullOnDelete();
            $table->foreignId('booking_slot_id')->constrained()->restrictOnDelete();

            $table->string('status', 30)->default('pending')->index();
            $table->string('payment_status', 30)->default('unpaid')->index();
            $table->string('source', 30)->default('web')->index();

            $table->string('customer_name');
            $table->string('customer_mobile', 20);
            $table->string('vehicle_plate_snapshot', 30)->nullable();
            $table->string('vehicle_type_snapshot')->nullable();

            $table->unsignedBigInteger('subtotal_amount')->default(0);
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->unsignedBigInteger('payable_amount')->default(0);
            $table->char('currency_code', 3)->default('IRR');

            $table->text('customer_note')->nullable();
            $table->text('internal_note')->nullable();

            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['car_wash_id', 'status', 'created_at']);
            $table->index(['customer_mobile', 'created_at']);
        });

        Schema::create('booking_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('car_wash_services')->nullOnDelete();
            $table->string('service_name');
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->unsignedBigInteger('unit_price');
            $table->unsignedSmallInteger('duration_minutes');
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->unsignedBigInteger('total_amount');
            $table->timestamps();
        });

        Schema::create('booking_status_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('from_status', 30)->nullable();
            $table->string('to_status', 30);
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['booking_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_status_histories');
        Schema::dropIfExists('booking_items');
        Schema::dropIfExists('bookings');
    }
};
