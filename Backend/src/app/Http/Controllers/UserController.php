<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
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
}
