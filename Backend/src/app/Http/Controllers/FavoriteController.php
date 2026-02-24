<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Listado de favoritos del usuario autenticado
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id_user;

        $favoritos = Favorite::where('id_user', $userId)
            ->with(['product:id_product,name', 'user:id_user,name'])
            ->get();

        return response()->json($favoritos, 200);
    }

    /**
     * Crear un favorito para el usuario autenticado
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_product' => 'required|integer|exists:products,id_product',
        ]);

        $userId = $request->user()->id_user;

        $favorito = Favorite::firstOrCreate([
            'id_user' => $userId,
            'id_product' => $data['id_product'],
        ]);

        return response()->json([
            'status' => 'true',
            'message' => 'Favorito creado correctamente',
            'data' => $favorito
        ], 201);
    }

    /**
     * Eliminar un favorito por su id (debe pertenecer al usuario)
     */
    public function destroy(Request $request, $id)
    {
        $userId = $request->user()->id_user;

        $favorito = Favorite::where('id_favorite', $id)
            ->where('id_user', $userId)
            ->first();

        if (!$favorito) {
            return response()->json([
                'status' => 'false',
                'message' => 'Favorito no encontrado'
            ], 404);
        }

        $favorito->delete();

        return response()->json([
            'status' => 'true',
            'message' => 'Favorito eliminado correctamente'
        ], 200);
    }
}

