<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Models\Delivery_Point;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;


class UserController extends Controller
{
    /**
     * Función para obtener todos los usuarios
     *
     * @return json
     */
    public function index()
    {
        // Obtenemos todos los usuarios de la base de datos
        $users = User::all();

        // Devolvemos la respuesta en formato json
        return response()->json($users);
    }

    /**
     * Función para obtener un usuario
     *
     * @param Request $request
     * @return json
     */
    public function show(Request $request)
    {
        // Obtenemos un usuario por id
        $user = $request->user();
        
        // Comprobamos que el usuario existe
        if (!$user) {
            return response()->json(['message' => 'No trobat'], 404);
        }
        
        // Devolvemos la respuesta en formato json
        return response()->json($user);
    }

    /**
     * Función para actualizar un usuario
     *
     * @param Request $request
     * @param numeric $id
     * @return json
     */
    public function update(Request $request, $id)
    {
        // Obtenemos el usuario por id
        $user = User::find($id);

        // Comprobamos que el usuario existe
        if (!$user) {
            return response()->json(['message' => 'No trobat'], 404);
        }

        // Validamos los datos antes de insertarlos en la base de datos
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string',
            'phone' => 'required|string'
        ]);

        // Actualizamos el usuario definitivamente
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone
        ]);

        // Devolvemos la respuesta en formato json
        return response()->json([
            'message' => 'Usuari actualitzat',
            'user' => $user
        ], 200);
    }

    /**
     * Función para eliminar un usuario por su id
     *
     * @param numeric $id
     * @return json
     */
    public function destroy($id)
    {
        // Obtenemos el usuario por id
        $user = User::find($id);

        // Comprobamos que el usuario existe
        if (!$user) {
            return response()->json(['message' => 'Usuari no trobat'], 404);
        }

        // Si el usuario tiene un perfil, lo eliminamos
        if ($user->profile) {
            $user->profile->delete();
        }

        // Eliminamos el usuario
        $user->delete();

        // Devolvemos la respuesta en formato json
        return response()->json(['message' => 'Usuari i perfil eliminats correctament'],200);
    }

    /**
     * Función para obtener la pantalla de perfil
     *
     * @param Request $request
     * @return json
     */
    public function myProfile(Request $request)
    {
        // Obtenemos el usuario
        $user = $request->user();

        // Comprobamos que el usuario existe
        if (!$user) {
            return response()->json([
                'message' => 'No autorizado'
            ], 401);
        }

        // Cargamos el perfil seleccionado
        $user->load('profile');

        // Devolvemos los datos en formato json
        return response()->json([
            'message' => 'Perfil obtingut correctament',
            'user' => $user
        ], 200);
    }

    /**
     * Función para actualizar un perfil
     *
     * @param Request $request
     * @return json
     */
    public function updateMyProfile(Request $request)
    {
        // Obtenemos el usuario por su id
        $user = $request->user();

        // Cogemos los datos del usuario
        $userData = $request->input('user');

        // Hacemos la validación de los datos
        $data = validator($userData, [
            'name' => 'required|string',
            'email' => 'required|string',
            'password' => 'required|string',
            'phone' => 'nullable|string',
            'profile.profile_img' => 'nullable|string',
        ])->validate();

        // Actualizamos los datos del usuario
        $user->update([
            'name' => $data['name'],
            'password' => Hash::make($data['password']),
            'email' => $data['email'],
            'phone' => $data['phone'] ?? $user->phone,
        ]);

        // Actualizamos los datos del perfil
        if (isset($data['profile']['profile_img'])) {
            $user->profile()->updateOrCreate(
                ['id_user' => $user->id_user],
                ['profile_img' => $data['profile']['profile_img']]
            );
        }

        // Refrescamos los atributos del usuario
        $user->refresh(); 

        // Cargamos el perfil actualizado
        $user->load('profile'); 

        // Devolvemos la respuesta en formato json
        return response()->json([
            'message' => 'Perfil actualizat',
            'user' => $user
        ]);
    }

    /**
     * Función para mostrar el mapa
     *
     * @param Request $request
     * @return json
     */
    public function mostrarMapa(Request $request)
    {
        // Obtenemos el usuario
        $user = $request->user();

        // Comprobamos que el usuario existe
        if (!$user) {
            return response()->json(['message' => 'No autorizado'], 401);
        }
        
        // Obtenemos los puntos de venta de ese usuario
        $puntos = Delivery_Point::where('id_user', $user->id_user)->get();

        // Devolvemos la respuesta en formato json
        return response()->json($puntos);
    }
}