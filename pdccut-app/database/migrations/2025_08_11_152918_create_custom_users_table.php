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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mobile_number')->nullable()->unique();
            $table->string('membership_number')->nullable()->unique();
            $table->string('national_code')->nullable()->unique();
            $table->decimal('share_amount', 15, 2)->nullable()->default(0);
            $table->integer('share_count')->nullable()->default(0);
            $table->decimal('annual_profit_amount', 15, 2)->nullable()->default(0);
            $table->decimal('profit_amount', 15, 2)->nullable()->default(0);
            $table->decimal('annual_payment', 15, 2)->nullable()->default(0);
            $table->string('password')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
