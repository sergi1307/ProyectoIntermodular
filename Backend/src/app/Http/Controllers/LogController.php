<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Logs;

class LogController extends Controller
{
    public function registreModificacio(Request $request)
    {
        if ($request->user()->name !== 'examen') {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        $userId = $request->query('id');
        $start = $request->query('created_at');
        $end = $request->query('end_at');
        $q = Logs::whereIn('action', ['register','modificacio'])
                 ->where('table_name', 'users');
        if ($userId) $q->where('id_user', $userId);
        if ($start) $q->where('created_at', '>=', $start);
        if ($end) $q->where('created_at', '<=', $end.' 23:59:59');
        $logs = $q->orderBy('created_at', 'desc')->get();
        return response()->json($logs, 200);
    }

    public function loginLogout(Request $request)
    {
        if ($request->user()->name !== 'examen') {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        $start = $request->query('created_at');
        $end = $request->query('end_at');
        $q = Logs::whereIn('action', ['login','logout'])->where('table_name', 'users');
        if ($start) $q->where('created_at', '>=', $start);
        if ($end) $q->where('created_at', '<=', $end.' 23:59:59');
        $logs = $q->orderBy('created_at', 'desc')->get();
        return response()->json($logs, 200);
    }
}
