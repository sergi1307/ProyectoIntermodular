<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function createUser(UserRequest $request)
    {
        // Creació del usuari mitjançant les dades introduides pel formulari
        $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'registration_date' => $request->registration_date,
                // Xifra la contrasenya per a que no siga una contrasenya en texet pla
                'password' => Hash::make($request->password),
             ]);
        // Retorna la contrasenya en JSON
        return response()->json([
            'status' => 'true',
            'message' => 'Usuari creat correctament',
            // Creació del token per al usuari al registrar-se
            'token' => $user->createToken('api-token')->plainTextToken,
        ], 200);
    }

    public function loginUser(LoginUserRequest $request)
    {
        // Condicional d'error si l'usuari s'enganya al posar les credencials
        if (!Auth::attempt($request->only(['email', 'password']))) {
            // Resposta en JSON
            return response()->json([
                'status' => 'false',
                'message' => 'Credencials incorrectes',
            // Error 401: "Credencials incorrectes"
            ], 401);
        }
        // Verifica el usuari
        $user = User::where('email', $request->email)->first();
        // Resposta en JSON
        return response()->json([
            'status' => 'true',
            'message' => 'Usuari autenticat correctament',
            // Creació del token per al usuari al iniciar sessió
            'token' => $user->createToken('api-token')->plainTextToken,
        ], 200);
    }

}