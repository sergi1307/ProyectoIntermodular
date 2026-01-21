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
     * Funció per a obtindre tots els usuaris
     *
     * @return json
     */
    public function index()
    {
        // Obtenim tots els usuaris de la base de dades
        $users = User::all();

        // Retornem una llista en format json dels usuaris
        return response()->json($users);
    }

    /**
     * Funció per a obtindre un usuari
     *
     * @param numeric $id
     * @return json
     */
    public function show($id)
    {
        // Obtenim un usuari per id
        $user = User::find($id);
        
        // Comprobem que l'usuari existeix
        if (!$user) {
            return response()->json(['message' => 'No trobat'], 404);
        }
        
        // Retornem la resposta amb l'usuari en format json
        return response()->json($user);
    }

    /**
     * Funció per a actualitzar un usuari
     *
     * @param Request $request
     * @param numeric $id
     * @return json
     */
    public function update(Request $request, $id)
    {
        // Obtenim l'usuari per id
        $user = User::find($id);

        // Comprobem que l'usuari existeix
        if (!$user) {
            return response()->json(['message' => 'No trobat'], 404);
        }

        // Validem les dades abans d'introduir-les en la base de dades
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string',
            'phone' => 'required|string'
        ]);

        // Actualitzem l'usuari definitivament
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone
        ]);

        // Retornem una resposta com que ha anat tot bé
        return response()->json([
            'message' => 'Usuari actualitzat',
            'user' => $user
        ], 200);
    }

    /**
     * Funció per a eliminar un usuari per id
     *
     * @param numeric $id
     * @return json
     */
    public function destroy($id)
    {
        // Obtenim l'usuari per id
        $user = User::find($id);

        // Comprobem que l'usuari existeix
        if (!$user) {
            return response()->json(['message' => 'Usuari no trobat'], 404);
        }

        if ($user->profile) {
            $user->profile->delete();
        }

        // Eliminem l'usuari
        $user->delete();

        // Retornem una resposta afirmativa de que ha anat tot bé
        return response()->json(['message' => 'Usuari i perfil eliminats correctament'],200);
    }

    /**
     * Funció per a obtindre la pantalla del perfil
     *
     * @param Request $request
     * @return json
     */
    public function myProfile(Request $request)
    {
        // Obtindre l'usuari autenticat
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'No autorizado'
            ], 401);
        }

        // Carrega el perfil relacionat
        $user->load('profile');

        // Retornar datos
        return response()->json([
            'message' => 'Perfil obtingut correctament',
            'user' => $user
        ], 200);
    }

    /**
     * Funció per a actualitzar el perfil
     *
     * @param Request $request
     * @return json
     */
    public function updateMyProfile(Request $request)
    {
        $user = $request->user();

        // Tomamos solo los datos dentro de 'user'
        $userData = $request->input('user');

        // Validación
        $data = validator($userData, [
            'name' => 'string',
            'email' => 'string',
            'password' => 'string',
            'phone' => 'string|nullable',
            'profile.profile_img' => 'string|nullable',
        ])->validate();

        // Actualizamos usuario
        $user->update([
            'name' => $data['name'],
            'password' => Hash::make($data['password']),
            'email' => $data['email'],
            'phone' => $data['phone'] ?? $user->phone,
        ]);

        // Actualizamos profile
        if (isset($data['profile']['profile_img'])) {
            $user->profile()->updateOrCreate(
                ['id_user' => $user->id_user],
                ['profile_img' => $data['profile']['profile_img']]
            );
        }

        // refresca els atributs del usuari
        $user->refresh(); 
        // Carrega el profile actualizado
        $user->load('profile'); 

        return response()->json([
            'message' => 'Perfil actualizado',
            'user' => $user
        ]);
    }

    /**
     * Funció per a mostrar el mapa
     *
     * @param Request $request
     * @return json
     */
    public function mostrarMapa(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'No autorizado'], 401);
        }
        
        $puntos = \App\Models\Delivery_Point::where('id_user', $user->id_user)->get();

        return response()->json($puntos);
    }

}
