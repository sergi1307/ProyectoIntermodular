<?php

namespace App\Http\Controllers;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function create(Request $request){
        $review = Review::create([
            'id_sale' => $request -> id_sale,
            'calification' => $request -> calification,
            'comment' => $request -> comment,
            'review_date' => $request -> review_date,
        ]);
        return response() ->json([
            'status' => 'true',
            'message' => 'punto de venta creat correctament',
        ],200);
    }
    public function index(){
        $review = Review::all();
        return response() ->json($review, 200);
    }
    public function show($id){
        $review = Review::findOrFail($id);
        return response() -> json($review, 200);
    }
    public function update(Request $request, $id){
        $review = Review::findOrFail($id);
        $review = Review::update([
            'id_sale' => $request -> id_sale,
            'calification' => $request -> calification,
            'comment' => $request -> comment,
            'review_date' => $request -> review_date,
        ]);
        return response() -> json([
            'message' => 'review eliminada'
        ],200);
    }
}
