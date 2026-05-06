<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function authenticate(Request $request)
    {
        $request->validate([
            'usuario' => 'required|email',
            'password' => 'required|string',
        ]);

        $usuario = Usuario::where('Email', $request->usuario)->first();

        if (! $usuario || ! Hash::check($request->password, $usuario->password)) {
            return redirect()->route('login')
                ->with('error', 'Credenciales incorrectas, por favor intente de nuevo.');
        }

        session(['usuario' => $usuario->only(['Email', 'nombre_usuario', 'nivel_permisos'])]);

        return redirect()->route('welcome')->with('success', 'Has iniciado sesión correctamente.');
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:ct_usuarios,Email',
            'nombre_usuario' => 'required|string|max:100',
            'password' => 'required|string|min:6|confirmed',
        ]);

        Usuario::create([
            'Email' => $request->email,
            'password' => Hash::make($request->password),
            'nombre_usuario' => $request->nombre_usuario,
            'nivel_permisos' => 'user',
        ]);

        return redirect()->route('login')->with('success', 'Registro exitoso. Ya puedes iniciar sesión.');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('usuario');
        return redirect()->route('login');
    }
}
