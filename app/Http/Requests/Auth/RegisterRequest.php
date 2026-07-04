<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'min:2', 'max:100'],
            'phone'    => ['required', 'string', 'min:11', 'max:20'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(5)],
            'consent'  => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Введите ваше имя',
            'name.min'           => 'Имя слишком короткое',
            'phone.required'     => 'Введите номер телефона',
            'phone.min'          => 'Введите корректный номер',
            'email.required'     => 'Введите электронную почту',
            'email.email'        => 'Некорректный адрес почты',
            'email.unique'       => 'Этот email уже зарегистрирован',
            'password.required'  => 'Введите пароль',
            'password.min'       => 'Пароль должен быть не менее 5 символов',
            'password.confirmed' => 'Пароли не совпадают',
            'consent.required'   => 'Необходимо согласие на обработку данных',
            'consent.accepted'   => 'Необходимо согласие на обработку данных',
        ];
    }
}
