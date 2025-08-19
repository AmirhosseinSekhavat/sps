<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EarnedProfit;

class EarnedProfitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample data based on the chart pattern from 1386 to 1403
        $profits = [
            1386 => 500,    // Very low
            1387 => 800,    // Very low
            1388 => 1200,   // Very low
            1389 => 1500,   // Very low
            1390 => 2000,   // Very low
            1391 => 2500,   // Very low
            1392 => 3000,   // Very low
            1393 => 3500,   // Very low
            1394 => 4000,   // Very low
            1395 => 4500,   // Very low
            1396 => 5000,   // Very low
            1397 => 8000,   // Slight increase
            1398 => 12000,  // Slight increase
            1399 => 18000,  // Starting growth
            1400 => 28000,  // Growth
            1401 => 45000,  // Significant growth
            1402 => 75000,  // Substantial growth
            1403 => 150000, // Highest peak
        ];

        foreach ($profits as $year => $amount) {
            EarnedProfit::updateOrCreate(
                ['year' => $year],
                [
                    'profit_type' => 'annual',
                    'amount' => $amount,
                    'description' => "سود اکتسابی سال {$year}",
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('EarnedProfit data seeded successfully!');
    }
}
