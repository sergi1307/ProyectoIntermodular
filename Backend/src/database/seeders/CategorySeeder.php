<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productosBase = [
            'Manzanas', 'Peras', 'Naranjas', 'Limones', 'Mandarinas', 
            'Fresas', 'Arándanos', 'Cerezas', 'Uvas', 'Sandías', 'Melones',
            'Melocotones', 'Ciruelas', 'Higos', 'Aguacates', 'Plátanos',
            
            'Tomates', 'Pimientos', 'Calabacines', 'Berenjenas', 'Pepinos',
            'Lechugas', 'Espinacas', 'Acelgas', 'Zanahorias', 'Patatas',
            'Cebollas', 'Ajos', 'Puerros', 'Brócoli', 'Coliflor',
            'Calabazas', 'Remolachas', 'Rábanos',
            
            'Huevos', 'Miel', 'Aceite de Oliva', 'Vino', 'Quesos',
            'Legumbres', 'Arroz', 'Trigo', 'Maíz', 'Nueces', 'Almendras'
        ];

        $adjetivos = [
            'Ecológicos',
            'de Temporada',
            'Premium',
            'Km0',
            'a Granel'
        ];

        $categorias = [];

        foreach ($productosBase as $producto) {
            
            $categorias[] = [
                'name' => $producto
            ];

            foreach ($adjetivos as $adjetivo) {
                
                $nombreCategoria = "$producto $adjetivo";
                
                $categorias[] = [
                    'name' => $nombreCategoria,
                ];
            }
        }

        foreach (array_chunk($categorias, 100) as $chunk) {
            DB::table('categories')->insert($chunk);
        }
    }
}
