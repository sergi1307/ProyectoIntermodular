<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Request/Petició
// Extiende de FormRequest
class UserRequest extends FormRequest
{
    /**
     * Definimos los campos a llenar en el formulario:
     * name: String obligatorio maximo 255 caracteres
     * email: String obligatorio maximo 255 caracteres único
     * password: String obligatorio
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'registration_date' => 'nullable|date',
            'password' => 'required|string'
        ];
    }
}
