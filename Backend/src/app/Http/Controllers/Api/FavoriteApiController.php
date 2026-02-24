<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteApiController extends Controller
{
    public function index()
    {
        return response()->json(
            Favorite::with(['user:id_user,name', 'product:id_product,name'])->paginate(20),
            200
        );
    }

    public function show(Favorite $favorite)
    {
        return response()->json($favorite->load(['user:id_user,name', 'product:id_product,name']), 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_user' => 'required|integer|exists:users,id_user',
            'id_product' => 'required|integer|exists:products,id_product',
        ]);

        $favorite = Favorite::create($data);

        return response()->json([
            'status' => 'true',
            'message' => 'Favorito creado correctamente',
            'data' => $favorite
        ], 201);
    }

    public function update(Request $request, Favorite $favorite)
    {
        $data = $request->validate([
            'id_user' => 'required|integer|exists:users,id_user',
            'id_product' => 'required|integer|exists:products,id_product',
        ]);

        $favorite->update($data);

        return response()->json([
            'status' => 'true',
            'message' => 'Favorito actualizado correctamente',
            'data' => $favorite
        ], 200);
    }

    public function destroy(Favorite $favorite)
    {
        $favorite->delete();

        return response()->json([
            'status' => 'true',
            'message' => 'Favorito eliminado correctamente'
        ], 200);
    }
}

