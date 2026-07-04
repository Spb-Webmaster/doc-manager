<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\IndividualEntrepreneur\Pages;

use App\Models\User;
use App\MoonShine\Resources\IndividualEntrepreneur\IndividualEntrepreneurResource;
use App\MoonShine\Resources\User\UserResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\Checkbox;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Phone;
use MoonShine\UI\Fields\Text;

/**
 * @extends FormPage<IndividualEntrepreneurResource>
 */
final class IndividualEntrepreneurFormPage extends FormPage
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
                        Flex::make([
                            Text::make('Наименование', 'name'),
                            Text::make('ФИО', 'full_name')->required(),
                        ]),

                        Flex::make([
                            Text::make('ИНН', 'inn')->required(),
                            Text::make('ОГРНИП', 'ogrnip'),
                            Text::make('ОКВЭД', 'okved'),
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

                    Tab::make('Налогообложение', [
                        Checkbox::make('Плательщик НДС', 'payment_nds'),
                        Text::make('Система налогообложения (ID)', 'taxation_id'),
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
            'user_id'   => ['required', 'integer', 'exists:users,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'inn'       => ['required', 'string', 'size:12'],
            'ogrnip'    => ['nullable', 'string', 'max:15'],
            'email'     => ['nullable', 'email'],
            'phone'     => ['nullable', 'string', 'max:20'],
            'bik'       => ['nullable', 'string', 'size:9'],
            'payment_account'      => ['nullable', 'string', 'max:20'],
            'correspondent_account' => ['nullable', 'string', 'max:20'],
        ];
    }
}
