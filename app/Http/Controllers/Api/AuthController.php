<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseApiController
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->sendError('Email atau password salah.', null, 401);
        }

        return $this->sendSuccess('Login berhasil.', [
            'token' => $user->createToken('api-token')->plainTextToken,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return $this->sendSuccess('Logout berhasil.', null);
    }

    public function me(Request $request): JsonResponse
    {
        return $this->sendSuccess('Data user berhasil diambil.', $request->user());
    }
}
