<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Funció que retorna tots els productes
     * 
     * @return json
     */
    public function index() {
        $products = Product::all();
        return response()->json($products);
    }

    /**
     * Funció que retorna un producte
     * 
     * @param number $id
     * @return json
     */
    public function show($id) {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    /**
     * Funció per a crear un producte mitjançant:
     * - id_user
     * - id_delivery_point
     * - name
     * - description
     * - price
     * - stock
     * - image
     * - type_stock
     * - state
     * - publicacion
     * 
     * @param Request $request
     * @return json
     */
    public function store(Request $request) {
        $product = Product::create([
            
        ])
    }
}
