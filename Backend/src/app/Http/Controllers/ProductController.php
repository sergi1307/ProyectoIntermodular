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
    public function index()
    {
        // Obtenim els productes amb els camps relacionats de les altres taules de la base de dades
        $products = Product::with(['user:id_user,name', 'delivery_point:id_delivery_point,name', 'category:id_category,name'])->get();

        // Retornme la resposta en JSON
        return response()->json($products);
    }

    /**
     * Funció que retorna un producte
     * 
     * @param int $id
     * @return json
     */
    public function show($id)
    {
        // Obtenim el producte amb els camps relacionats de les altres taules de la base de dades buscat per id
        $product = Product::with(['user:id_user,name', 'delivery_point:id_delivery_point,name', 'category:id_category,name'])->findOrFail($id);

        // Retornem la resposta en JSON
        return response()->json($product);
    }

    /**
     * Funció per a crear un producte
     * 
     * @param Request $request
     * @return json
     */
    public function store(Request $request)
    {
        // Creem el producte amb els camps: id_user, id_delivery_point, id_category, name, description, price, stock, image, type_stock, state, publication_date
        $product = Product::create([
            'id_user' => $request->id_user,
            'id_delivery_point' => $request->id_delivery_point,
            'id_category' => $request->id_category,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $request->image,
            'type_stock' => $request->type_stock,
            'state' => $request->state,
            'publication_date' => $request->publication_date
        ]);

        // Retornem la resposta en JSON en cas de que no done error
        return response()->json([
            'status' => 'true',
            'message' => 'Producte creat correctament'
        ], 200);
    }

    /**
     * Funció per a actualitzar un producte
     * 
     * @param Request $request
     * @param int $id
     */
    public function update(Request $request, $id)
    {
        // Busquem el producte per id
        $product = Product::findOrFail($id);

        // Comprobem que el producte existeix
        if (!$product) {
            return response()->json([
                'status' => 'false',
                'message' => 'Este producte no existeix'
            ], 404);
        }

        // Actualitzem les dades del producte
        $product->update([
            'id_user' => $request->id_user,
            'id_delivery_point' => $request->id_delivery_point,
            'id_category' => $request->id_category,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $request->image,
            'type_stock' => $request->type_stock,
            'state' => $request->state,
            'publication_date' => $request->publication_date
        ]);

        // Retornem en cas afirmatiu un json
        return response()->json([
            'message' => 'Producte actualitzat correctament.'
        ], 200);
    }

    /**
     * Funció per a eliminar un producte per id
     * 
     * @param int $id
     * @return json
     */
    public function destroy($id)
    {
        // Busquem el producte per id
        $product = Product::findOrFail($id);

        // Comprobem que el producte existeix
        if (!$product) {
            return response()->json([
                'status' => 'false',
                'message' => 'Este producte no existeix'
            ], 404);
        }

        // Eliminem el producte de la base de dades
        $product->delete();

        // En cas de que no done error retornem una resposta json
        return response()->json([
            'status' => 'true',
            'message' => 'Producte eliminat correctament'
        ], 200);
    }
}
