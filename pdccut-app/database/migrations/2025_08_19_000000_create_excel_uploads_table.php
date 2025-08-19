<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('excel_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('original_name');
            $table->string('stored_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->unsignedInteger('financial_year');
            $table->string('status')->default('success'); // success|failed
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->index(['financial_year', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('excel_uploads');
    }
}; 