
<?php

namespace App\Http;

use App\Models\Delivery_Point;
use App\Models\Product;

public function mostrarMapa()
    {
        // Usamos 'with' para traer los puntos y sus productos en una sola operación.
        // Esto evita hacer cientos de consultas separadas a la base de datos (evita problema N+1).
        $puntos = Delivery_Point::with('products')->get();
        // Si no usara el with, Laravel tendría que ir a la base de datos una vez por cada punto para buscar sus productos. Con with, lo hace todo de golpe y la web va más rápida
        // Enviamos el paquete completo a la vista
        return view('mapa', compact('puntos'));
    }