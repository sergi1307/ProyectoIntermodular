<?php

namespace App\Http\Controllers;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // 1. Crear una reseña
    public function create(Request $request){
        $review = Review::create([
            'id_sale' => $request -> id_sale,
            'calification' => $request -> calification,
            'comment' => $request -> comment,
            'review_date' => $request -> review_date,
        ]);
        return response() ->json([
            'status' => 'true',
            'message' => 'review creat correctament',
        ],200);
    }
    // 2. Mostrar todas las reseña
    public function index(){
        $review = Review::all();
        return response() ->json($review, 200);
    }
    // 3. Mostrar una reseña
    public function show($id){
        $review = Review::findOrFail($id);
        return response() -> json($review, 200);
    }
    // 4. Actualizar una reseña
    public function update(Request $request, $id){
        $review = Review::findOrFail($id);
        $review ->update([
            'id_sale' => $request -> id_sale,
            'calification' => $request -> calification,
            'comment' => $request -> comment,
            'review_date' => $request -> review_date,
        ]);
        return response() -> json([
            'message' => 'review actualizada'
        ],200);
    }
    // 5. Eliminar una reseña
    public function destroy($id){
    $review = Review::findOrFail($id);
    $review->delete();
        return response()-> json([
            'message' => 'Venta eliminada'
        ],200);
    }
}
