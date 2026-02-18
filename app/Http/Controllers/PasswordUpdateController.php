<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use App\Models\PasswordResetToken;

class PasswordUpdateController extends Controller
{
    // Redefine a senha usando o token
    public function reset(Request $request, $token)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $usuario = Usuario::where('email', $request->email)->first();
        if (!$usuario) {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }

        $tokenRow = PasswordResetToken::where('email', $usuario->email)->where('token', $token)->first();
        if (!$tokenRow) {
            return response()->json(['message' => 'Token inválido ou expirado.'], 400);
        }

        $usuario->senha = Hash::make($request->password);
        $usuario->save();

        PasswordResetToken::where('email', $usuario->email)->delete();

        return response()->json(['message' => 'Senha redefinida com sucesso.']);
    }
}
