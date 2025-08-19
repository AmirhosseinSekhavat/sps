<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create admin user
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'father_name' => 'Admin',
            'mobile_number' => '09123456789',
            'membership_number' => 'ADMIN001',
            'national_code' => '1234567890',
            'password' => Hash::make('admin123'),
            'is_active' => true,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove admin user
        User::where('national_code', '1234567890')->delete();
    }
};
