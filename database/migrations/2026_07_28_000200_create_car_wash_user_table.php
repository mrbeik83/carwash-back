<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_wash_user', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('car_wash_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status', 30)->default('invited')->index();
            $table->string('job_title')->nullable();
            $table->foreignId('invited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->timestamps();

            $table->unique(['car_wash_id', 'user_id']);
            $table->index(['user_id', 'status']);
            $table->index(['car_wash_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_wash_user');
    }
};
