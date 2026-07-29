<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capacity_rules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('car_wash_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('weekday');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('slot_duration_minutes')->default(60);
            $table->unsignedSmallInteger('capacity')->default(1);
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['car_wash_id', 'weekday', 'is_active']);
        });

        Schema::create('schedule_exceptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('car_wash_id')->constrained()->cascadeOnDelete();
            $table->date('exception_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->unsignedSmallInteger('capacity_override')->nullable();
            $table->string('reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['car_wash_id', 'exception_date']);
        });

        Schema::create('booking_slots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('car_wash_id')->constrained()->cascadeOnDelete();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->unsignedSmallInteger('capacity');
            $table->unsignedSmallInteger('reserved_count')->default(0);
            $table->string('status', 20)->default('open')->index();
            $table->string('source', 20)->default('rule');
            $table->timestamps();

            $table->unique(['car_wash_id', 'starts_at']);
            $table->index(['car_wash_id', 'starts_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_slots');
        Schema::dropIfExists('schedule_exceptions');
        Schema::dropIfExists('capacity_rules');
    }
};
