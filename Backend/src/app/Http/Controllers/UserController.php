<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}
