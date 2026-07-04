<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\SelfEmployed\Pages;

use App\Models\User;
use App\MoonShine\Resources\SelfEmployed\SelfEmployedResource;
use App\MoonShine\Resources\User\UserResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Phone;
use MoonShine\UI\Fields\Text;

/**
 * @extends FormPage<SelfEmployedResource>
 */
final class SelfEmployedFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make([
                ID::make(),

                BelongsTo::make('Пользователь', 'user', fn(User $u) => "{$u->name} ({$u->email})", UserResource::class)
                    ->required()
                    ->searchable(),

                Tabs::make([
                    Tab::make('Основное', [
                        Text::make('ФИО', 'full_name')->required(),

                        Flex::make([
                            Text::make('ИНН', 'inn')->required(),
                        ]),

                        Flex::make([
                            Text::make('Адрес регистрации', 'register_address'),
                            Text::make('Фактический адрес', 'address'),
                        ]),

                        Flex::make([
                            Email::make('Email', 'email'),
                            Phone::make('Телефон', 'phone'),
                        ]),
                    ]),

                    Tab::make('Паспорт', [
                        Flex::make([
                            Text::make('Серия', 'passport_serial'),
                            Text::make('Номер', 'passport_number'),
                        ]),

                        Text::make('Кем выдан', 'who_issued'),

                        Date::make('Дата выдачи', 'date_issued')
                            ->format('d.m.Y'),
                    ]),

                    Tab::make('Банк', [
                        Text::make('Банк', 'bank'),
                        Flex::make([
                            Text::make('Расчётный счёт', 'payment_account'),
                            Text::make('БИК', 'bik'),
                            Text::make('Корр. счёт', 'correspondent_account'),
                        ]),
                    ]),
                ]),
            ]),
        ];
    }

    protected function rules(mixed $item): array
    {
        return [
            'user_id'        => ['required', 'integer', 'exists:users,id'],
            'full_name'      => ['required', 'string', 'max:255'],
            'inn'            => ['required', 'string', 'size:12'],
            'email'          => ['nullable', 'email'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'passport_serial' => ['nullable', 'string', 'max:10'],
            'passport_number' => ['nullable', 'string', 'max:10'],
            'date_issued'    => ['nullable', 'date'],
            'bik'            => ['nullable', 'string', 'size:9'],
            'payment_account'      => ['nullable', 'string', 'max:20'],
            'correspondent_account' => ['nullable', 'string', 'max:20'],
        ];
    }
}
