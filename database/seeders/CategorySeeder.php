<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Main categories
            ['name' => 'Masculino', 'slug' => 'masculino', 'parent_id' => null, 'is_active' => true],
            ['name' => 'Feminino', 'slug' => 'feminino', 'parent_id' => null, 'is_active' => true],
            ['name' => 'Formatura', 'slug' => 'formatura', 'parent_id' => null, 'is_active' => true],
            ['name' => 'Casamento', 'slug' => 'casamento', 'parent_id' => null, 'is_active' => true],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Subcategories for Masculino
        $masculino = Category::where('slug', 'masculino')->first();
        if ($masculino) {
            Category::create(['name' => 'Anéis', 'slug' => 'aneis-masculino', 'parent_id' => $masculino->id, 'is_active' => true]);
            Category::create(['name' => 'Colares', 'slug' => 'colares-masculino', 'parent_id' => $masculino->id, 'is_active' => true]);
            Category::create(['name' => 'Pulseiras', 'slug' => 'pulseiras-masculino', 'parent_id' => $masculino->id, 'is_active' => true]);
        }

        // Subcategories for Feminino
        $feminino = Category::where('slug', 'feminino')->first();
        if ($feminino) {
            Category::create(['name' => 'Anéis', 'slug' => 'aneis-feminino', 'parent_id' => $feminino->id, 'is_active' => true]);
            Category::create(['name' => 'Colares', 'slug' => 'colares-feminino', 'parent_id' => $feminino->id, 'is_active' => true]);
            Category::create(['name' => 'Brincos', 'slug' => 'brincos-feminino', 'parent_id' => $feminino->id, 'is_active' => true]);
            Category::create(['name' => 'Pulseiras', 'slug' => 'pulseiras-feminino', 'parent_id' => $feminino->id, 'is_active' => true]);
        }
    }
}
