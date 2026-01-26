<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Request/Petició
class LoginUserRequest extends FormRequest
{
    /**
     * Definimos los campos para rellenar el formulario
     * email: String obligatorio maximo 255 carácteres
     * password: String obligatorio
     */
    public function rules()
    {
        return [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string'
        ];
    }
}
