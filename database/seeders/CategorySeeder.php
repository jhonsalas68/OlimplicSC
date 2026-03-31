<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['nombre' => 'Pre Infantil', 'edad_min' => 12,  'edad_max' => 13],
            ['nombre' => 'Infantil',     'edad_min' => 14, 'edad_max' => 15],
            ['nombre' => 'Menores',      'edad_min' => 16, 'edad_max' => 17],
            ['nombre' => 'Juvenil',      'edad_min' => 18, 'edad_max' => 19],
            ['nombre' => 'Libre',        'edad_min' => 20, 'edad_max' => 99],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['nombre' => $cat['nombre']], $cat);
        }
    }
}
