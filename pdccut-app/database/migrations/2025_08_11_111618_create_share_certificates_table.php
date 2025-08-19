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
        Schema::create('share_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('year');
            $table->decimal('share_amount', 15, 2);
            $table->integer('share_count');
            $table->decimal('annual_profit_amount', 15, 2);
            $table->decimal('annual_payment', 15, 2);
            $table->string('pdf_path')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('share_certificates');
    }
};
