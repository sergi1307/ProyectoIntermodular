<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Función para registrar un usuario
     *
     * @param UserRequest $request
     * @return json
     */
    public function createUser(UserRequest $request)
    {
        try {
            // Creación del usuario mediante las inserciones del formulario con la contraseña encriptada 
            $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'registration_date' => $request->registration_date,
                    'password' => Hash::make($request->password),
                ]);
            $user->log()->create([
                'action'     => 'create',
                'table_name' => 'users',
                'data'       => 'Usuario creado correctamente',
                'ip_address' => $request->ip(),
                'create_at'  => now(),
            ]);

            $user->profile()->create([
            // Inicializamos las variables del perfil vacías
            'profile_img' => null, 
            ]);
            
            // Creamos el token a partir del usuario
            $token = $user->createToken('api-token')->plainTextToken;

            // Insertem el token en las cookies
            $cookie = cookie('auth_token', $token, 9999, '/', null, false, true);

            // Respuesta en json
            return response()->json([
                'status' => 'true',
                'message' => 'Usuari creat correctament',
                'token' => $token,
                'user' => $user->load('profile'),
            ], 200)->withCookie($cookie);
        } catch(Exception $e) {
            return response()->json([
                'status' => 'false',
                'message' => 'Error al crear el usuari',
                'error' => $e,
            ], 200);
        }
    }

    /**
     * Función de login para usuarios
     *
     * @param LoginUserRequest $request
     * @return json
     */
    public function loginUser(LoginUserRequest $request)
    {
        try {
            if (!Auth::attempt($request->only(['email', 'password']))) {
                
                return response()->json([
                    'status' => 'false',
                    'message' => 'Credencials incorrectes',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            $token = $user->createToken('api-token')->plainTextToken;

            $cookie = cookie('auth_token', $token, 9999, '/', null, false, true);

            $user->log()->create([
                'action'     => 'login',
                'table_name' => 'users',
                'data'       => 'Usuario logueado correctamente',
                'ip_address' => $request->ip(),
                'create_at'  => now(),
            ]);

            // Respuesta en json
            return response()->json([
                'status' => 'true',
                'message' => 'Usuari autenticat correctamente',
                'token' => $token,
                'user' => $user,
            ], 200)->withCookie($cookie);
            
        } catch (Exception $e) {
            return response()->json([
                'status' => 'false',
                'message' => 'Error al autenticar el usuari',
                'error' => $e->getMessage(),
            ], 500);
        }  
    }

    /**
     * Función para cerrar sesión
     *
     * @param Request $request
     * @return json
     */
    public function logoutUser(Request $request)
    {
        try {
            $user = $request->user();
            if ($user) {
                $user->log()->create([
                    'action'     => 'logout',
                    'table_name' => 'users',
                    'data'       => 'usuario desconectado correctamente',
                    'ip_address' => $request->ip(),
                    'create_at'  => now(),
                ]);
            }

            $user->currentAccessToken()->delete();

            $cookie = cookie()->forget('auth_token');

            return response()->json([
                'status' => 'true',
                'message' => 'Sessió tancada correctament'
            ])->withCookie($cookie);
            
        } catch (Exception $e) {
            return response()->json([
                'status' => 'false',
                'message' => 'Error al tancar la sessió',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}