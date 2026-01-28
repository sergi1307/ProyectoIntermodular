<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ProductController extends Controller
{
    /**
     * Función para obtener los productos de un usuario
     *
     * @param Request $request
     * @return json
     */
    public function myProducts(Request $request)
    {
        // Obtenemos el id del usuario
        $userId = $request->user()->id_user ?? $request->id_user;

        // Buscamos los productos con los demás campos necesarios
        $products = Product::where('id_user', $userId)
            ->with(['category', 'delivery_point:id_delivery_point,name'])
            ->orderBy('publication_date', 'desc')
            ->get();

        // Devolvemos la respuesta en formato json
        return response()->json([
            'status' => 'true',
            'data' => $products
        ], 200);
    }

    /**
     * Función que devuelve todos los productos
     * 
     * * @return json
     */
    public function index()
    {
        // Obtenemos los productos con sus campos relacionados
        $products = Product::with(['user:id_user,name', 'delivery_point:id_delivery_point,name,direction,latitude,length', 'category'])
            ->paginate(20);

        // Devolvemos la respuesta en formato json
        return response()->json($products, 200);
    }

    /**
     * Función que devuelve un producto
     * 
     * @param int $id
     * @return json
     */
    public function show($id)
    {
        // Obtenemos el producto con sus campos relacionados
        $product = Product::with(['user:id_user,name', 'delivery_point:id_delivery_point,name', 'category'])->findOrFail($id);

        // Devolvemos la respuesta en formato json
        return response()->json($product);
    }

    /**
     * Función para crear un producto
     * 
     * @param Request $request
     * @return json
     */
    public function store(Request $request)
    {
        // Seleccionamos la fecha de hoy
        $fecha = Carbon::now('Europe/Madrid')->format('Y-m-d');

        // Obtenemos el id del usuario
        $userId = $request->user()->id_user ?? $request->id_user;

        // Validamos los datos antes de enviarlos
        $validated = $request->validate([
            'id_delivery_point'  => 'required|integer|exists:delivery_points,id_delivery_point',
            'id_category' => 'nullable|exists:categories,id_category',
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:51200',
            'type_stock' => 'required|in:Kg,Unidad',
            'state' => 'required|in:Agotado,Reservado,Disponible'
        ]);

        // Inicializamos la variable de la ruta de la imagen
        $rutaImagen = null;

        // Comprobamos si se ha enviado alguna imagen
        if ($request->hasFile('image')) {
            // Guardamos la imagen en la carpeta storage, y la ruta en la base de datos
            $rutaImagen = $request->file('image')->store('products', 'public');
        }

        // Creamos el producto con sus campos relacionados
        $product = Product::create([
            'id_user' => $userId,
            'id_delivery_point' => $request->id_delivery_point,
            'id_category' => $request->id_category,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $rutaImagen,
            'type_stock' => $request->type_stock,
            'state' => $request->state,
            'publication_date' => $fecha
        ]);

        // Devolvemos la respuesta en formato json
        return response()->json([
            'status' => 'true',
            'message' => 'Producte creat correctament'
        ], 200);
    }

    /**
     * Función para actualizar un producto
     * 
     * @param Request $request
     * @param int $id
     */
    public function update(Request $request, $id)
    {
        // Buscamos el producto por id
        $product = Product::findOrFail($id);
        
        // Comprobamos que el producto pertenenzca realmente al usuario
        if ($product->id_user !== $request->user()->id_user) {
            return response()->json(['message' => 'No autorizat'], 403);
        }   

        // Validamos los datos
        $validated = $request->validate([
            'id_delivery_point'  => 'integer|exists:delivery_points,id_delivery_point',
            'id_category' => 'nullable|exists:categories,id_category',
            'name' => 'string',
            'description' => 'string',
            'price' => 'numeric',
            'stock' => 'integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:51200',
            'type_stock' => 'in:Kg,Unidad',
            'state' => 'in:Agotado,Reservado,Disponible'
        ]);

        // Asignamos la ruta antigua de la imagen a la variable
        $rutaImagen = $product->image;

        // Comprobamos si hay alguna imagen nueva
        if ($request->hasFile('image')) {
            // Si la hay, eliminamos la imagen anterior de la carpeta storage
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            // Guardamos la nueva imagen
            $rutaImagen = $request->file('image')->store('products', 'public');
        }

        // Actualizamos los datos del producto
        $product->update([
            'id_delivery_point' => $request->input('id_delivery_point', $product->id_delivery_point),
            'id_category' => $request->input('id_category', $product->id_category),
            'name' => $request->input('name', $product->name),
            'description' => $request->input('description', $product->description),
            'price' => $request->input('price', $product->price),
            'stock' => $request->input('stock', $product->stock),
            'image' => $rutaImagen,
            'type_stock' => $request->input('type_stock', $product->type_stock),
            'state' => $request->input('state', $product->state)
        ]);

        // Devolvemos la respuesta en formato json
        return response()->json([
            'message' => 'Producte actualitzat correctament.'
        ], 200);
    }

    /**
     * Función para eliminar un producto
     * 
     * @param int $id
     * @return json
     */
    public function destroy(Request $request, $id)
    {
        // Buscamos el producto por id
        $product = Product::findOrFail($id);

        // Comprobamos que el producto pertenezca a ese usuario
        if ($product->id_user !== $request->user()->id_user) {
            return response()->json(['message' => 'No autorizat'], 403);
        }

        // Eliminamos la imagen que ya existía
        if ($product->image && Storage::disk('public')->exists($product->image)) {
             Storage::disk('public')->delete($product->image);
        }

        // Eliminamos el producto
        $product->delete();

        // Devolvemos la respuesta en formato json
        return response()->json([
            'status' => 'true',
            'message' => 'Producte eliminat correctament'
        ], 200);
    }
}