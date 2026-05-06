<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Usuario::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'Email' => 'required|email|unique:ct_usuarios,Email',
            'password' => 'required|string|min:8',
            'nombre_usuario' => 'required|string|max:100',
            'nivel_permisos' => 'string|max:20',
        ]);

        $usuario = Usuario::create([
            'Email' => $request->Email,
            'password' => Hash::make($request->password),
            'nombre_usuario' => $request->nombre_usuario,
            'nivel_permisos' => $request->nivel_permisos ?? 'user',
        ]);

        return response()->json($usuario, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Usuario::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $usuario = Usuario::findOrFail($id);

        $request->validate([
            'Email' => 'email|unique:ct_usuarios,Email,' . $id . ',Email',
            'password' => 'nullable|string|min:8',
            'nombre_usuario' => 'string|max:100',
            'nivel_permisos' => 'string|max:20',
        ]);

        $data = $request->only(['Email', 'nombre_usuario', 'nivel_permisos']);
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        return response()->json($usuario);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->delete();

        return response()->json(['message' => 'Usuario eliminado']);
    }
}
