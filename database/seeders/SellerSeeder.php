<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SellerSeeder extends Seeder
{
    public function run(): void
    {
        $sellers = [
            [
                'name' => 'Dr. Maria Santos',
                'email' => 'devnight0710@gmail.com',
                'phone' => '(11) 98765-4321',
                'account_type' => 'professional',
            ],
            [
                'name' => 'Dr. João Silva',
                'email' => 'dollycookie0710@gmail.com',
                'phone' => '(21) 97654-3210',
                'account_type' => 'professional',
            ],
            [
                'name' => 'Associação MedGarden',
                'email' => 'tom8jerry0913@gmail.com',
                'phone' => '(31) 96543-2109',
                'account_type' => 'association',
            ],
        ];

        foreach ($sellers as $sellerData) {
            User::create([
                'name' => $sellerData['name'],
                'email' => $sellerData['email'],
                'phone' => $sellerData['phone'],
                'password' => Hash::make('password123'),
                'role' => 'seller',
                'account_type' => $sellerData['account_type'],
                'seller_status' => 'approved',
                'seller_approved_at' => now(),
                'is_active' => true,
            ]);
        }

        echo "Created " . count($sellers) . " professionals/associations successfully!\n";
    }
}
