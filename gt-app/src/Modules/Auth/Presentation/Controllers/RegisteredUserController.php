<?php

namespace Modules\Auth\Presentation\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Auth\Application\DTOs\RegisterUserDTO;
use Modules\Auth\Application\UseCases\RegisterUserUseCase;
use Modules\User\Infrastructure\Models\User;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, RegisterUserUseCase $useCase): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $dto = new RegisterUserDTO(
            name: $request->name,
            email: $request->email,
            password: $request->password,
        );

        $user = $useCase->execute($dto);

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
