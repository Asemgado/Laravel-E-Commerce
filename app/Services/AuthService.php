<?php

namespace App\Services;

use App\Models\User;
use App\Enums\UserRolesEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class AuthService
{
    public function register(array $data): array
    {
        $existUser = User::where('email', $data['email'])->first();

        if($existUser) {
            throw ValidationException::withMessages([
                'email' => ['you can not use this email']
            ]);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role']
        ]);


        return [
            'user' => $user,
            'token' => $user->createToken('api-token')->plainTextToken,
        ];
    }

    public function login(array $credentials): array
    {
        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $user = Auth::user();

        return [
            'user' => $user,
            'token' => $user->createToken('api-token')->plainTextToken,
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    
    public function confirmEmail(string $email): void
    {
        $user = User::where('email', $email)->first();

        if (! $user) {
            throw new InvalidArgumentException('there is no user with this email');
        }
        $user->confirmEmail();
       
    }
}