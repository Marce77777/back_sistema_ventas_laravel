<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function ingresar(Request $request)
    {
        //validar
        $credenciales = $request->validate([
           "email"=>"required|string|email",
           "password"=>"required"
        ]);
        //verificar
           if(!Auth::attempt($credenciales)){
              return response()->json([
                "mensaje"=>"No Autorizado"
              ],401);
           }
        //generar token
        $user = $request->user();
        $resultadoToken = $user->createToken('Token de Acceso');
        $token = $resultadoToken->plainTextToken;
        //responder
        return response()->json([
            "acces_token" =>$token,
            "token_type" => "Bearer",
            "usuario" =>$user
        ]);
    }
    public function registrar(Request $request)
    {
        $request->validate([
            "name" => "required|string",
            "email" => "required|string|unique:users|email",
            "password" => "required"
        ]);
        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);

            if ($user->save()) {
                return response()->josn(["mensaje" => "Usuario Registrado"], 201);
            } else {
                return response()->json(["mensaje" => "Ocurrio un error al registrar el usuario"], 422);
            }
        } catch (\Exception $error) {
            return response()->json([
                "mensaje" => "Ocurrio un error al registrar el usuario",
                "error" => $error
            ], 500);
        }
    }
    public function perfil(Request $request)
    {
        $user = Auth::user();
        return response()->json($user);
    }
    public function salir(Request $request)
    {
        $user = Auth::user()->tokens()->delete();
        return response()->json([
            "mensaje" => "Log out"
        ],200);

    }
}
