<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_wash_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('car_wash_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('booking_interval_minutes')->default(60);
            $table->unsignedInteger('minimum_booking_notice_minutes')->default(60);
            $table->unsignedSmallInteger('maximum_booking_days_ahead')->default(30);
            $table->unsignedInteger('cancellation_deadline_minutes')->default(120);
            $table->unsignedSmallInteger('default_capacity')->default(1);
            $table->boolean('auto_confirm_booking')->default(true);
            $table->boolean('allow_guest_booking')->default(true);
            $table->boolean('require_online_payment')->default(false);
            $table->boolean('send_sms_notifications')->default(true);
            $table->json('extra')->nullable();
            $table->timestamps();
        });

        Schema::create('car_wash_invitations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('car_wash_id')->constrained()->cascadeOnDelete();
            $table->string('mobile', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('role_name', 80);
            $table->string('token_hash', 64)->unique();
            $table->foreignId('invited_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('expires_at');
            $table->foreignId('accepted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['car_wash_id', 'expires_at']);
            $table->index(['mobile', 'expires_at']);
            $table->index(['email', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_wash_invitations');
        Schema::dropIfExists('car_wash_settings');
    }
};
