<?php

namespace Modules\Auth\Application\UseCases;

use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Modules\Auth\Application\DTOs\RegisterUserDTO;
use Modules\User\Infrastructure\Models\User;

class RegisterUserUseCase
{
    public function execute(RegisterUserDTO $dto): User
    {
        $user = User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
        ]);

        // Default role assignment
        $user->assignRole('staff');

        event(new Registered($user));

        return $user;
    }
}
