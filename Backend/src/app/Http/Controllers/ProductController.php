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
        $products = Product::with(['user:id_user,name', 'delivery_point:id_delivery_point,name', 'category:id_category,name'])->get();
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
        $product = Product::with(['user:id_user,name', 'delivery_point:id_delivery_point,name', 'category:id_category,name'])->findOrFail($id);
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
        $product = Product::findOrFail($id);

        if (!$product) {
            return response()->json([
                'status' => 'false',
                'message' => 'Este producte no existeix'
            ], 404);
        }

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
        $product = Product::findOrFail($id);

        if (!$product) {
            return response()->json([
                'status' => 'false',
                'message' => 'Este producte no existeix'
            ], 404);
        }

        $product->delete();
        return response()->json([
            'status' => 'true',
            'message' => 'Producte eliminat correctament'
        ], 200);
    }
}
