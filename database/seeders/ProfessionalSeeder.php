<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfessionalSeeder extends Seeder
{
    /**
     * Seed professional, association, and store users
     */
    public function run(): void
    {
        $professionals = [
            [
                'name' => 'Dr. Maria Santos',
                'email' => 'devnight0710@gmail.com',
                'phone' => '(11) 98765-4321',
                'role' => 'professional',
            ],
            [
                'name' => 'Dr. João Silva',
                'email' => 'dollycookie0710@gmail.com',
                'phone' => '(21) 97654-3210',
                'role' => 'professional',
            ],
            [
                'name' => 'Associação MedGarden',
                'email' => 'tom8jerry0913@gmail.com',
                'phone' => '(31) 96543-2109',
                'role' => 'association',
            ],
            [
                'name' => 'Loja MedGarden',
                'email' => 'store@medgardenshop.com',
                'phone' => '(41) 95432-1098',
                'role' => 'store',
            ],
        ];

        foreach ($professionals as $professionalData) {
            User::create([
                'name' => $professionalData['name'],
                'email' => $professionalData['email'],
                'phone' => $professionalData['phone'],
                'password' => Hash::make('password123'),
                'role' => $professionalData['role'],
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        $this->command->info('Created ' . count($professionals) . ' professionals/associations/stores successfully!');
    }
}
