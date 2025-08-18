<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Vêtements Hommes',
                'description' => 'Collection complète de vêtements pour hommes',
                'slug' => 'vetements-hommes'
            ],
            [
                'name' => 'Vêtements Femmes',
                'description' => 'Collection élégante de vêtements pour femmes',
                'slug' => 'vetements-femmes'
            ],
            [
                'name' => 'Vêtements Enfants',
                'description' => 'Vêtements confortables et stylés pour enfants',
                'slug' => 'vetements-enfants'
            ],
            [
                'name' => 'Chaussures',
                'description' => 'Chaussures de qualité pour tous les âges',
                'slug' => 'chaussures'
            ],
            [
                'name' => 'Accessoires',
                'description' => 'Accessoires de mode et bijoux',
                'slug' => 'accessoires'
            ],
            [
                'name' => 'Sous-vêtements',
                'description' => 'Sous-vêtements confortables et élégants',
                'slug' => 'sous-vetements'
            ],
            [
                'name' => 'Sport & Fitness',
                'description' => 'Vêtements et équipements de sport',
                'slug' => 'sport-fitness'
            ],
            [
                'name' => 'Maillots de Bain',
                'description' => 'Maillots de bain et vêtements de plage',
                'slug' => 'maillots-bain'
            ],
            [
                'name' => 'Vêtements de Mariage',
                'description' => 'Robes de mariée et costumes de cérémonie',
                'slug' => 'vetements-mariage'
            ],
            [
                'name' => 'Vêtements Traditionnels',
                'description' => 'Vêtements traditionnels et ethniques',
                'slug' => 'vetements-traditionnels'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
