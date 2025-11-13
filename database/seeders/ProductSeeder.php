<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;
use App\Models\GoldPrice;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Get all users (any role can create products)
        $users = User::all();

        if ($users->isEmpty()) {
            echo "No users found. Please run UserSeeder first.\n";
            return;
        }

        $latestGoldPrice = GoldPrice::getLatest();
        $currentGoldPrice = $latestGoldPrice?->price_gram_18k ?? 99.17;

        $products = $this->getProductsData();
        $statuses = ['pending', 'approved', 'rejected'];
        $imageUrls = $this->getImageUrls();
        $descriptions = $this->getDescriptions();

        $karats = ['18k', '18k', '18k', '14k', '14k', '10k'];

        foreach ($products as $index => $productData) {
            $user = $users->random();
            $status = $statuses[array_rand($statuses)];
            $karat = $karats[array_rand($karats)];
            $currentPrice = $productData['base_price'];

            $images = [
                $imageUrls[array_rand($imageUrls)],
                $imageUrls[array_rand($imageUrls)],
                $imageUrls[array_rand($imageUrls)],
            ];

            Product::create([
                'user_id' => $user->id,
                'name' => $productData['name'],
                'description' => $descriptions[array_rand($descriptions)],
                'base_price' => $productData['base_price'],
                'current_price' => $currentPrice,
                'gold_weight_grams' => $productData['gold_weight'],
                'gold_karat' => $karat,
                'initial_gold_price' => $currentGoldPrice,
                'category' => $productData['category'],
                'images' => json_encode($images),
                // Use external GitHub repository for 3D model files
                'model_3d_url' => 'https://github.com/nightmare5831/public_asset/releases/download/3d-models-v1/jewelry.glb',
                'model_3d_type' => 'glb',
                'stock_quantity' => rand(5, 50),
                'is_active' => true,
                'status' => $status,
                'approved_by' => $status !== 'pending' ? 1 : null,
                'approved_at' => $status !== 'pending' ? now() : null,
                'rejection_reason' => $status === 'rejected' ? 'Imagem de baixa qualidade' : null,
            ]);
        }

        echo "Created " . count($products) . " products successfully!\n";
    }

    private function getProductsData(): array
    {
        return [
            // Masculino
            ['name' => 'Anel Masculino em Ouro 18k', 'category' => 'Masculino', 'gold_weight' => 5.5, 'base_price' => 2500.00],
            ['name' => 'Anel Solitário Masculino', 'category' => 'Masculino', 'gold_weight' => 6.0, 'base_price' => 2800.00],
            ['name' => 'Anel Masculino com Pedra', 'category' => 'Masculino', 'gold_weight' => 7.2, 'base_price' => 3200.00],
            ['name' => 'Corrente Grumet Masculina', 'category' => 'Masculino', 'gold_weight' => 15.0, 'base_price' => 5500.00],
            ['name' => 'Corrente Cartier Masculina', 'category' => 'Masculino', 'gold_weight' => 12.5, 'base_price' => 4800.00],
            ['name' => 'Pulseira Masculina Grossa', 'category' => 'Masculino', 'gold_weight' => 10.0, 'base_price' => 4200.00],
            ['name' => 'Pulseira Masculina Cartier', 'category' => 'Masculino', 'gold_weight' => 8.5, 'base_price' => 3800.00],

            // Feminino
            ['name' => 'Anel Solitário Feminino', 'category' => 'Feminino', 'gold_weight' => 3.5, 'base_price' => 1800.00],
            ['name' => 'Anel Meia Aliança', 'category' => 'Feminino', 'gold_weight' => 4.0, 'base_price' => 2100.00],
            ['name' => 'Anel de Noivado', 'category' => 'Feminino', 'gold_weight' => 3.2, 'base_price' => 1650.00],
            ['name' => 'Corrente Veneziana Feminina', 'category' => 'Feminino', 'gold_weight' => 6.0, 'base_price' => 2800.00],
            ['name' => 'Corrente Cartier Feminina', 'category' => 'Feminino', 'gold_weight' => 5.5, 'base_price' => 2600.00],
            ['name' => 'Brinco Argola Média', 'category' => 'Feminino', 'gold_weight' => 3.0, 'base_price' => 1600.00],
            ['name' => 'Brinco Argola Grande', 'category' => 'Feminino', 'gold_weight' => 4.5, 'base_price' => 2100.00],
            ['name' => 'Brinco Solitário', 'category' => 'Feminino', 'gold_weight' => 2.5, 'base_price' => 1400.00],
            ['name' => 'Pulseira Feminina Delicada', 'category' => 'Feminino', 'gold_weight' => 5.0, 'base_price' => 2400.00],
            ['name' => 'Pulseira Cartier Feminina', 'category' => 'Feminino', 'gold_weight' => 6.0, 'base_price' => 2750.00],

            // Formatura
            ['name' => 'Anel de Formatura Direito', 'category' => 'Formatura', 'gold_weight' => 8.5, 'base_price' => 3800.00],
            ['name' => 'Anel de Formatura Medicina', 'category' => 'Formatura', 'gold_weight' => 9.0, 'base_price' => 4000.00],

            // Casamento
            ['name' => 'Aliança de Casamento Lisa', 'category' => 'Casamento', 'gold_weight' => 4.0, 'base_price' => 1900.00],
            ['name' => 'Aliança de Casamento Trabalhada', 'category' => 'Casamento', 'gold_weight' => 4.5, 'base_price' => 2100.00],
        ];
    }

    private function getImageUrls(): array
    {
        return [
            'https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=400',
            'https://images.unsplash.com/photo-1611591437281-460bfbe1220a?w=400',
            'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=400',
            'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=400',
            'https://images.unsplash.com/photo-1617038220319-276d3cfab638?w=400',
        ];
    }

    private function getDescriptions(): array
    {
        return [
            'Peça em ouro 18k de alta qualidade, produzida com técnicas artesanais.',
            'Joia elegante e sofisticada, perfeita para ocasiões especiais.',
            'Design moderno e atemporal, que combina com qualquer estilo.',
            'Acabamento impecável e detalhes refinados.',
            'Peça única que representa tradição e exclusividade.',
        ];
    }
}
