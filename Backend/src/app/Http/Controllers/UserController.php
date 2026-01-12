<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Models\Delivery_Point;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Controlador
    /**
    * Crea un usuari mitjançant:
    *  - nom
    *  - correu electrònic
    *  - numero de telefon
    *  - contrassenya
    */
    public function createUser(UserRequest $request)
    {
        // Passa el nom, correu i contrassenya per a crear-lo
        $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'registration_date' => $request->registration_date,
                'password' => Hash::make($request->password),
             ]);
        // Resposta en forma de JSON
        return response()->json([
            'status' => 'true',
            'message' => 'Usuari creat correctament'
        ], 200);
    }
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }
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

        return response()->json(['message' => 'Usuario eliminado'],200);
    }

    public function mostrarMapa() {
        // Usamos 'with' para traer los puntos y sus productos en una sola operación.
        // Esto evita hacer cientos de consultas separadas a la base de datos (evita problema N+1).
        $puntos = Delivery_Point::with('products')->get();
        // Si no usara el with, Laravel tendría que ir a la base de datos una vez por cada punto para buscar sus productos. Con with, lo hace todo de golpe y la web va más rápida
        // Enviamos el paquete completo a la vista
        return view('mapa', compact('puntos'));
    }
}
