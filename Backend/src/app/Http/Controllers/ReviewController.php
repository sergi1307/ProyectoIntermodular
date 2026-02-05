<?php

namespace App\Http\Controllers;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Sale;

class ReviewController extends Controller
{
    /**
     * Función para crear una review
     *
     * @param Request $request
     * @return json
     */
    public function store(Request $request)
    {
        // Obtenemos la fecha de hoy
        $fecha = Carbon::now('Europe/Madrid')->format('Y-m-d');

        // Validamos los datos antes de introducirlos en la base de datos
        $validated = $request->validate([
            'id_sale' => 'required|integer|exists:sales,id_sale',
            'calification' => 'required|numeric|min:0|max:5|regex:/^[0-5](\.[05])?$/',
            'comment' => 'required'
        ]);

        // Insertamos definitivamente los datos
        $review = Review::create([
            'id_sale' => $request -> id_sale,
            'calification' => $request -> calification,
            'comment' => $request -> comment,
            'review_date' => $fecha
        ]);

        // Devolvemos la respuesta en formato json
        return response() ->json([
            'status' => 'true',
            'message' => 'review creat correctament',
        ], 200);
    }

    /**
     * Función para obtener todas las reviews
     *
     * @return json
     */
    public function index()
    {
        // Obtenemos todas las reviews
        $review = Review::all();

        // Devolvemos la lista en formato json
        return response() ->json($review, 200);
    }

    /**
     * Función para obtener una review
     *
     * @param numeric $id
     * @return json
     */
    public function show($id)
    {
        // Obtenemos la review por id
        $review = Review::findOrFail($id);

        // Devolvemos la respuesta en formato json
        return response() -> json($review, 200);
    }

    /**
     * Función para actualizar una review
     *
     * @param Request $request
     * @param numeric $id
     * @return json
     */
    public function update(Request $request, $id)
    {
        // Obtenemos la review por id
        $review = Review::findOrFail($id);

        // Validamos los datos antes de introducirlos en la base de datos
        $validated = $request->validate([
            'id_sale' => 'required|exists:sales,id_sale',
            'calification' => 'required|numeric|min:0|max:5|regex:/^[0-5](\.[05])?$/',
            'comment' => 'required'
        ]);

        // Insertamos los datos definitivamente en la base de datos
        $review ->update([
            'id_sale' => $request -> id_sale,
            'calification' => $request -> calification,
            'comment' => $request -> comment
        ]);

        // Devolvemos la respuesta en formato json
        return response() -> json([
            'message' => 'review actualizada'
        ], 200);
    }

    /**
     * Función para eliminar una review
     *
     * @param numeric $id
     * @return json
     */
    public function destroy($id)
    {
        // Obtenemos la review por id
        $review = Review::findOrFail($id);

        // Borramos definitivamente la review
        $review->delete();

        // Devolvemos la respuesta en formato json
        return response()-> json([
            'message' => 'Venta eliminada'
        ], 200);
    }
    /**
     * Función para obtener las reviews de un producto
     *
     * @param numeric $productId
     * @return json
     */
    public function obtenerPorProducto($productId)
    {
        $ventasIds = Sale::where('id_product', $productId)->pluck('id_sale');
    
        $reviews = Review::whereIn('id_sale', $ventasIds)
        ->with('sale.buyer:id_user,name')
        ->orderBy('review_date', 'desc')
        ->paginate(10);
    
    return response()->json($reviews, 200);
    }

    /**
     * Función para obtener la media de calificaciones de un producto
     *
     * @param numeric $productId
     * @return json
     */
    public function obtenerMediaPorProducto($productId)
    {
        $ventasIds = Sale::where('id_product', $productId)->pluck('id_sale');

        $media = Review::whereIn('id_sale', $ventasIds)
            ->avg('calification');
        
        $total = Review::whereIn('id_sale', $ventasIds)->count();

        return response()->json([
            'average' => round($media, 2),
            'total_reviews' => $total
        ], 200);
    }
}
