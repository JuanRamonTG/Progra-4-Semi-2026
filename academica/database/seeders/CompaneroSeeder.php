<?php

namespace Database\Seeders;

use App\Models\Companero;
use Illuminate\Database\Seeder;

class CompaneroSeeder extends Seeder
{
    public function run(): void
    {
        $companeros = [
            ['nombre' => 'Ramon'],
            ['nombre' => 'Kerin'],
            ['nombre' => 'Hamilton'],
            ['nombre' => 'Brian'],
            ['nombre' => 'Olimpia'],
            ['nombre' => 'Caleb'],
        ];

        foreach ($companeros as $companero) {
            Companero::create($companero);
        }
    }
}
