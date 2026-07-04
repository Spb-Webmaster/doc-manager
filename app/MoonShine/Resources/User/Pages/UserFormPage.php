<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\User\Pages;

use App\Models\User;
use App\MoonShine\Resources\User\UserResource;
use Illuminate\Validation\Rule;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Components\Collapse;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Password;
use MoonShine\UI\Fields\PasswordRepeat;
use MoonShine\UI\Fields\Phone;
use MoonShine\UI\Fields\Text;

/**
 * @extends FormPage<UserResource, User>
 */
final class UserFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make([
                ID::make(),

                Flex::make([
                    Text::make('Имя', 'name')->required(),
                    Email::make('Email', 'email')->required(),
                ]),

                Phone::make('Телефон', 'phone'),

                Collapse::make('Изменить пароль', [
                    // onApply передаёт чистый текст — каст 'hashed' в модели сам хеширует,
                    // чтобы не было двойного хеширования
                    Password::make('Пароль', 'password')
                        ->customAttributes(['autocomplete' => 'new-password'])
                        ->eye()
                        ->onApply(function (User $item, mixed $value): User {
                            if (!empty($value)) {
                                $item->password = $value;
                            }
                            return $item;
                        }),

                    PasswordRepeat::make('Повторите пароль', 'password_confirmation')
                        ->customAttributes(['autocomplete' => 'confirm-password'])
                        ->eye(),
                ])->icon('lock-closed'),
            ]),
        ];
    }

    protected function rules(DataWrapperContract $item): array
    {
        /** @var User $user */
        $user = $item->getOriginal();

        return [
            'name'  => ['required', 'string', 'min:2', 'max:100'],
            'email' => [
                'required',
                'email',
                Rule::unique(User::class, 'email')->ignore($user?->id),
            ],
            'phone'    => ['nullable', 'string', 'min:11', 'max:20'],
            'password' => [
                ...($item->getKey() !== null ? ['sometimes', 'nullable'] : ['required']),
                'min:5',
                'confirmed',
            ],
        ];
    }
}
