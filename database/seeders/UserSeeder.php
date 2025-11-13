<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed all regular users (patient, professional, association, store)
     * Super admin is seeded separately in SuperAdminSeeder
     */
    public function run(): void
    {
        $users = [
            // Patients (4 users)
            [
                'name' => 'Carlos Oliveira',
                'email' => 'carlos.patient@example.com',
                'phone' => '(11) 91234-5678',
                'role' => 'patient',
            ],
            [
                'name' => 'Patricia Lima',
                'email' => 'patricia.patient@example.com',
                'phone' => '(21) 92345-6789',
                'role' => 'patient',
            ],
            [
                'name' => 'Roberto Almeida',
                'email' => 'roberto.patient@example.com',
                'phone' => '(31) 93456-7890',
                'role' => 'patient',
            ],
            [
                'name' => 'Fernanda Costa',
                'email' => 'fernanda.patient@example.com',
                'phone' => '(41) 94567-8901',
                'role' => 'patient',
            ],

            // Professionals (2 users)
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

            // Association (1 user)
            [
                'name' => 'Associação MedGarden',
                'email' => 'tom8jerry0913@gmail.com',
                'phone' => '(31) 96543-2109',
                'role' => 'association',
            ],

            // Store (1 user)
            [
                'name' => 'Loja MedGarden',
                'email' => 'store@medgardenshop.com',
                'phone' => '(41) 95432-1098',
                'role' => 'store',
            ],
        ];

        foreach ($users as $userData) {
            User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'phone' => $userData['phone'],
                'password' => Hash::make('password123'),
                'role' => $userData['role'],
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        // Count users by role
        $roleCounts = [
            'patient' => 0,
            'professional' => 0,
            'association' => 0,
            'store' => 0,
        ];

        foreach ($users as $user) {
            $roleCounts[$user['role']]++;
        }

        echo "Created users successfully:\n";
        echo "  - Patients: {$roleCounts['patient']}\n";
        echo "  - Professionals: {$roleCounts['professional']}\n";
        echo "  - Associations: {$roleCounts['association']}\n";
        echo "  - Stores: {$roleCounts['store']}\n";
        echo "  Total: " . count($users) . " users\n";
    }
}
