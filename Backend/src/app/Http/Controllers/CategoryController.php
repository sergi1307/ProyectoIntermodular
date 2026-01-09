<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Funció que retorna totes les categoríes
     *
     * @return json
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    /**
     * Funció que retorna una categoria
     *
     * @param int $id
     * @return json
     */
    public function show($id) {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    /**
     * Funció per a crear una categoria mitjançant:
     * - name
     * 
     * @param Request $request
     * @return json
     */
    public function store(Request $request) {
        $category = Category::create([
            'name'
        ]);

        return response()->json([
            'status' => 'true',
            'message' => 'Categoria creada correctamente'
        ], 200);
    }

    /**
     * Funció per a actualitzar una categoria mitjançant:
     * - name
     * 
     * @param Request $request
     * @param int $id_categorie
     * @return json
     */
    public function update(Request $request, $id) {
        $category = Category::findOrFail($id);

        if (!$category) {
            return response()->json(['message' => 'Esta categoria no existeix']);
        }

        $datos = [
            'name' => $request->name
        ];
        $category->update($datos);

        return response()->json([
            'message' => 'Categoria actualitzada correctament'
        ]);
    }

    /**
     * Funció per a eliminar una categoria per Id
     * 
     * @param int $id
     * @return json
     */
    public function destroy($id) {
        $category = Category::findOrFail($id);

        if(!$category) {
            return response()->json(['message' => 'Esta categoria no existeix']);
        }

        $category->delete();
        return response()->json(['message' => 'Categoria eliminada correctament']);
    }
}
