<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();
            $table->foreignId('booking_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('amount');
            $table->char('currency_code', 3)->default('IRR');
            $table->string('method', 20);
            $table->string('gateway', 50)->nullable();
            $table->string('status', 30)->default('pending')->index();
            $table->string('authority')->nullable()->index();
            $table->string('reference_id')->nullable()->index();
            $table->string('transaction_id')->nullable()->unique();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->unsignedBigInteger('refunded_amount')->default(0);
            $table->timestamp('refunded_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->json('gateway_response')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
