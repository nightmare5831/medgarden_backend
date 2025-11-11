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
        // Seed all tables in correct order
        $this->call([
            // 1. Email templates (independent)
            EmailTemplateSeeder::class,

            // 2. Users (SuperAdmin, Professionals/Associations, and Patients)
            SuperAdminSeeder::class,
            SellerSeeder::class, // Creates Professionals and Associations
            PatientSeeder::class, // Creates Patients

            // 3. Gold prices (independent)
            GoldPriceSeeder::class,

            // 4. Categories (independent)
            CategorySeeder::class,

            // 5. Products (depends on sellers, gold prices, categories)
            ProductSeeder::class,

            // 6. Messages (depends on users)
            MessageSeeder::class,
        ]);

        echo "\n✅ All seeders completed successfully!\n";
    }
}
