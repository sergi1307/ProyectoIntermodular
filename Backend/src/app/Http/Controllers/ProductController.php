<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ProductController extends Controller
{
    /**
     * Funció per a obtindre els productes d'un usuari
     *
     * @param Request $request
     * @return json
     */
    public function myProducts(Request $request)
    {
        // Obtenim l'id de l'usuari per mig del token
        $userId = $request->user()->id_user ?? $request->id_user;

        // Busquem els seus productes amb les relacions necesàries
        // NOTA: 'category' debe coincidir con el nombre de la función en tu Modelo Product
        $products = Product::where('id_user', $userId)
            ->with(['category', 'delivery_point:id_delivery_point,name'])
            ->orderBy('publication_date', 'desc')
            ->get();

        // Retornem la resposta en format json
        return response()->json([
            'status' => 'true',
            'data' => $products
        ], 200);
    }

    /**
     * Funció que retorna tots els productes
     * * @return json
     */
    public function index()
    {
        // Obtenim els productes amb els camps relacionats de les altres taules de la base de dades
        // Usem paginate per a no saturar la resposta
        $products = Product::with(['user:id_user,name', 'delivery_point:id_delivery_point,name,direction,latitude,length', 'category'])
            ->paginate(20);

        // Retornme la resposta en JSON
        return response()->json($products, 200);
    }

    /**
     * Funció que retorna un producte
     * * @param int $id
     * @return json
     */
    public function show($id)
    {
        // Obtenim el producte amb els camps relacionats de les altres taules de la base de dades buscat per id
        $product = Product::with(['user:id_user,name', 'delivery_point:id_delivery_point,name', 'category'])->findOrFail($id);

        // Retornem la resposta en JSON
        return response()->json($product);
    }

    /**
     * Funció per a crear un producte
     * * @param Request $request
     * @return json
     */
    public function store(Request $request)
    {
        // Seleccionamos la fecha de hoy
        $fecha = Carbon::now('Europe/Madrid')->format('Y-m-d');

        // SEGURIDAD: Obtenim l'id de l'usuari per mig del token (no del request directo)
        $userId = $request->user()->id_user ?? $request->id_user;

        // Validem les dades abans d'insertar el producte en la base de dades 
        $validated = $request->validate([
            'id_delivery_point'  => 'required|integer|exists:delivery_points,id_delivery_point',
            'id_category' => 'nullable|exists:categories,id_category', // Corregido a plural 'categories'
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'type_stock' => 'required|in:Kg,Unidad',
            'state' => 'required|in:Agotado,Reservado,Disponible'    
        ]);

        // Inicialitzem la variable de la ruta de la imatge
        $rutaImagen = null;

        // Comprobem que la imatge haja sigut enviada
        if ($request->hasFile('image')) {
            // Guardem l'imatge en la carpeta Storage i guardem la ruta en la base de dades
            $rutaImagen = $request->file('image')->store('products', 'public');
        }

        // Creem el producte amb els camps
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

        // Retornem la resposta en JSON en cas de que no done error
        return response()->json([
            'status' => 'true',
            'message' => 'Producte creat correctament'
        ], 200);
    }

    /**
     * Funció per a actualitzar un producte
     * * @param Request $request
     * @param int $id
     */
    public function update(Request $request, $id)
    {
        // Busquem el producte per id
        $product = Product::findOrFail($id);

        

        if ($product->id_user !== $request->user()->id_user) {
            return response()->json(['message' => 'No autorizat'], 403);
        }   

        // Validem les dades enviades del formulari
        $validated = $request->validate([
            'id_delivery_point'  => 'integer|exists:delivery_points,id_delivery_point',
            'id_category' => 'nullable|exists:categories,id_category',
            'name' => 'string',
            'description' => 'string',
            'price' => 'numeric',
            'stock' => 'integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'type_stock' => 'in:Kg,Unidad',
            'state' => 'in:Agotado,Reservado,Disponible'
        ]);

        // Assignem la ruta a la imatge que ja teniem abans
        $rutaImagen = $product->image;

        // Comprobem si s'ha enviat una imatge nova
        if ($request->hasFile('image')) {
            // Eliminem la imatge anterior per a reservar espai
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            // Guardem la nova imatge
            $rutaImagen = $request->file('image')->store('products', 'public');
        }

        // Actualitzem les dades del producte
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

        // Retornem en cas afirmatiu un json
        return response()->json([
            'message' => 'Producte actualitzat correctament.'
        ], 200);
    }

    /**
     * Funció per a eliminar un producte per id
     * * @param int $id
     * @return json
     */
    public function destroy(Request $request, $id)
    {
        // Busquem el producte per id
        $product = Product::findOrFail($id);

        if ($product->id_user !== $request->user()->id_user) {
            return response()->json(['message' => 'No autorizat'], 403);
        }

        // Eliminem l'imatge que ja existía
        if ($product->image && Storage::disk('public')->exists($product->image)) {
             Storage::disk('public')->delete($product->image);
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