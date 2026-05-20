<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return response()->json($request->user());
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'             => 'sometimes|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u',
            'email'            => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone'            => 'sometimes|string|regex:/^\+[1-9]\d{1,14}$/',
            'shipping_address' => 'sometimes|string|min:5|max:255',
            'billing_address'  => 'sometimes|string|min:5|max:255',
            'document_id'      => 'sometimes|string|max:50',
            'birth_date'       => 'sometimes|date|before_or_equal:' . now()->subYears(18)->format('Y-m-d') . '|after:' . now()->subYears(100)->format('Y-m-d'),
        ]);

        $user->update($data);

        return response()->json([
            'message' => 'Perfil actualizado correctamente.',
            'user'    => $user->fresh(),
        ]);
    }
}
