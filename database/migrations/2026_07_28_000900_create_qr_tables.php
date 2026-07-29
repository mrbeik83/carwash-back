<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_links', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('car_wash_id')->constrained()->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->string('type', 30)->default('booking');
            $table->string('title');
            $table->string('campaign')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['car_wash_id', 'is_active']);
        });

        Schema::create('qr_scans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('qr_link_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('referrer')->nullable();
            $table->timestamp('scanned_at')->useCurrent();

            $table->index(['qr_link_id', 'scanned_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_scans');
        Schema::dropIfExists('qr_links');
    }
};
