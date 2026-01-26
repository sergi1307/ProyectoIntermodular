<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Función que devuelve todas las categorías
     *
     * @return json
     */
    public function index()
    {
        // Obtenemos todas las categorías
        $categories = Category::all();

        // Devolvemos las categorías en formato json
        return response()->json($categories);
    }

    /**
     * Función que devuelve una categoría
     *
     * @param int $id
     * @return json
     */
    public function show($id)
    {
        // Buscamos la categoría por id
        $category = Category::findOrFail($id);

        // Devolvemos la categoría en formato json
        return response()->json($category);
    }

    /**
     * Función para crear una categoría
     * 
     * @param Request $request
     * @return json
     */
    public function store(Request $request)
    {
        // Creamos la categoría 
        $category = Category::create([
            'name' => $request->name
        ]);

        // Devolvemos una respuesta afirmativa en formato json
        return response()->json([
            'status' => 'true',
            'message' => 'Categoria creada correctamente'
        ], 200);
    }

    /**
     * Función para actualizar una categoría
     * 
     * @param Request $request
     * @param int $id
     * @return json
     */
    public function update(Request $request, $id)
    {
        // Buscamos la categoría por id
        $category = Category::findOrFail($id);

        // Comprobamos que la categoría existe
        if (!$category) {
            return response()->json(['message' => 'Esta categoria no existeix']);
        }

        // Actualizamos la categoría
        $category->update([
            'name' => $request->name
        ]);

        // Retornamos una respuesta afirmativa en formato json
        return response()->json([
            'status' => 'true',
            'message' => 'Categoria actualitzada correctament'
        ], 200);
    }

    /**
     * Función para eliminar una categoría
     * 
     * @param int $id
     * @return json
     */
    public function destroy($id)
    {
        // Buscamos la categoría por id
        $category = Category::findOrFail($id);

        // Comprobamos que la categoría existe
        if(!$category) {
            return response()->json([
                'status' => 'false',
                'message' => 'Esta categoria no existeix'
            ], 404);
        }

        // Eliminamos la categoría
        $category->delete();

        // Devolvemos una respuesta afirmativa en formato json
        return response()->json([
            'status' => 'true',
            'message' => 'Categoria eliminada correctament'
        ], 200);
    }
}
