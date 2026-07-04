<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\User\Pages;

use App\MoonShine\Resources\User\UserResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Phone;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<UserResource>
 */
final class UserIndexPage extends IndexPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),

            Text::make('Имя', 'name')->sortable(),

            Email::make('Email', 'email')->sortable(),

            Phone::make('Телефон', 'phone'),

            Date::make('Зарегистрирован', 'created_at')
                ->format('d.m.Y')
                ->sortable(),
        ];
    }

    protected function filters(): iterable
    {
        return [
            Email::make('Email', 'email'),
            Text::make('Имя', 'name'),
        ];
    }

    protected function modifyListComponent(ComponentContract $component): TableBuilder
    {
        return $component->columnSelection();
    }
}
