<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SefazCearaController extends Controller
{
    // Minimal stub to satisfy route discovery. Implement actual logic later if needed.
    public function consultarPorChave($chave)
    {
        return response()->json([
            'success' => false,
            'message' => 'SefazCearaController stub: endpoint not implemented',
            'chave' => $chave
        ], 501);
    }
}
