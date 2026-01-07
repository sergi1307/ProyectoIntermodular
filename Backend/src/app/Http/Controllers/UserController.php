<?php
namespace App\Http\Controllers;
use App\Models\Delivery_Point;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    // Get de todos los usuarios
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }
    // Post de un uasrio al servidor
    public function store(Request $request)
    {
        

        //crear usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password), // Encriptamos aquí directo
            'phone' => $request->phone,
            'registration_date' => $request->registration_date,
        ]);
        //respuesta si todo sale bien.
        return response()->json([
            'message' => 'Usuario creado correctamente',
            'user' => $user
        ], 201);
    }

    // Get de un usuario
    public function show($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json(['message' => 'No encontrado'], 404);
        }
        
        return response()->json($user);
    }

    // Actualizar un usuario por id
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'No encontrado'], 404);
        }

        // Preparamos los datos en el array para actualizarlos
        $datos = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'registration_date' => $request->registration_date,
        ];
        $user->update($datos);
        return response()->json([
            'message' => 'Usuario actualizado',
            'user' => $user
        ]);
    }

    // eliminar un usario por id
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'No encontrado'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'Usuario eliminado']);
    }
    public function mostrarMapa()
    {
        // Usamos Delivery_Point para sacar los puntos de venta y sus productos
        $puntos = Delivery_Point::with('products')->get();

        // Llamamos a la vista 'mapapuntosdeVenta' pasándole los datos compactados de los productos y puntos de venta.
        return view('mapapuntosdeVenta', compact('puntos'));
    }
}