<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\SelfEmployed\Pages;

use App\Models\User;
use App\MoonShine\Resources\SelfEmployed\SelfEmployedResource;
use App\MoonShine\Resources\User\UserResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Phone;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<SelfEmployedResource>
 */
final class SelfEmployedIndexPage extends IndexPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Пользователь', 'user', fn(User $u) => "{$u->name} ({$u->email})", UserResource::class),

            Text::make('ФИО', 'full_name')->sortable(),

            Text::make('ИНН', 'inn'),

            Email::make('Email', 'email'),

            Phone::make('Телефон', 'phone'),
        ];
    }

    protected function filters(): iterable
    {
        return [
            Text::make('ФИО', 'full_name'),
            Text::make('ИНН', 'inn'),
            Email::make('Email', 'email'),
        ];
    }

    protected function modifyListComponent(ComponentContract $component): TableBuilder
    {
        return $component->columnSelection();
    }
}
