<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patients = [
            [
                'name' => 'Carlos Oliveira',
                'email' => 'carlos.patient@example.com',
                'phone' => '(11) 91234-5678',
            ],
            [
                'name' => 'Patricia Lima',
                'email' => 'patricia.patient@example.com',
                'phone' => '(21) 92345-6789',
            ],
            [
                'name' => 'Roberto Almeida',
                'email' => 'roberto.patient@example.com',
                'phone' => '(31) 93456-7890',
            ],
            [
                'name' => 'Fernanda Costa',
                'email' => 'fernanda.patient@example.com',
                'phone' => '(41) 94567-8901',
            ],
        ];

        foreach ($patients as $patientData) {
            User::create([
                'name' => $patientData['name'],
                'email' => $patientData['email'],
                'phone' => $patientData['phone'],
                'password' => Hash::make('password123'),
                'role' => 'patient',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        echo "Created " . count($patients) . " patients successfully!\n";
    }
}
