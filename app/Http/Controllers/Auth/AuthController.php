<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Регистрация, вход и выход пользователей личного кабинета.
 */
class AuthController extends Controller
{
    /**
     * GET /register — страница формы регистрации (только для гостей).
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * POST /register — создание пользователя.
     *
     * Валидация — в RegisterRequest. После создания сразу авторизует
     * пользователя и перенаправляет в кабинет.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = User::create($request->only('name', 'phone', 'email', 'password'));

        auth()->login($user);

        return redirect()->route('cabinet');
    }

    /**
     * GET /login — страница формы входа (только для гостей).
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * POST /login — вход по email и паролю (с опцией «запомнить меня»).
     *
     * При успехе регенерирует сессию и ведёт в кабинет (или на intended-URL);
     * при неудаче показывает flash-сообщение и возвращает на форму.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        if (auth()->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('cabinet'));
        }

        flash()->alert(config('message_flash.alert.login_error'));

        return back()->onlyInput('email');
    }

    /**
     * POST /logout — выход: сброс сессии и CSRF-токена, redirect на главную.
     */
    public function logout(Request $request): RedirectResponse
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
