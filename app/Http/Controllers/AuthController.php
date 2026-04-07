<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u',
            'birth_date'       => 'required|date|before_or_equal:' . now()->subYears(18)->format('Y-m-d') . '|after:' . now()->subYears(100)->format('Y-m-d'),
            'phone'            => 'required|string|regex:/^\+[1-9]\d{1,14}$/',
            'shipping_address' => 'required|string|min:5|max:255',
            'billing_address'  => 'required|string|min:5|max:255',
            'document_id'      => 'required|string|max:50',
            'referral_code'    => 'nullable|string|max:50',
            'email'            => 'required|string|email|max:255|unique:users',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'             => $request->name,
            'birth_date'       => $request->birth_date,
            'phone'            => $request->phone,
            'shipping_address' => $request->shipping_address,
            'billing_address'  => $request->billing_address,
            'document_id'      => $request->document_id,
            'referral_code'    => $request->referral_code,
            'email'            => $request->email,
            'password'         => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales son incorrectas.'],
            ]);
        }

        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente.'
        ]);
    }
}
