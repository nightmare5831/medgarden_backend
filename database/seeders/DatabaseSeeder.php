<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            EmailTemplateSeeder::class,
            SuperAdminSeeder::class,    
            UserSeeder::class,           
            GoldPriceSeeder::class,
            ProductSeeder::class,
            MessageSeeder::class,
        ]);

        echo "\n✅ All seeders completed successfully!\n";
    }
}
