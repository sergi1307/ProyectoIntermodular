<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;
use App\Models\User;

class logController extends Controller
{
    public function index()
    {
        $equipos = Log::with(['usuario'])->get();
        return response()->json($equipos, 200);
    }

    public function show($id)
    {
        $equipo = Log::with([
            'usuario',
        ])->findOrFail($id);

        return response()->json($equipo, 200);
    }
}