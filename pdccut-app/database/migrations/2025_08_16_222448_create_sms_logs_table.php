<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('national_code')->nullable()->index();
            $table->string('mobile_number')->index();
            $table->string('type')->default('otp'); // otp, notification
            $table->text('message')->nullable();
            $table->string('otp_code')->nullable();
            $table->string('status')->default('pending'); // pending, sent, failed, delivered
            $table->string('provider')->default('melipayamak');
            $table->string('provider_response_id')->nullable();
            $table->text('provider_response')->nullable();
            $table->string('ip_address')->nullable();
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['type', 'status']);
            $table->index(['mobile_number', 'created_at']);
            $table->index(['national_code', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
