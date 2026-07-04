<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Act\Pages;

use App\Enums\ActStatus;
use App\Models\Contractor;
use App\Models\Invoice;
use App\Models\User;
use App\MoonShine\Resources\Act\ActResource;
use App\MoonShine\Resources\Contractor\ContractorResource;
use App\MoonShine\Resources\Invoice\InvoiceResource;
use App\MoonShine\Resources\User\UserResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<ActResource>
 */
final class ActIndexPage extends IndexPage
{
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Пользователь', 'user', fn(User $u) => "{$u->name} ({$u->email})", UserResource::class),

            BelongsTo::make('Контрагент', 'contractor', fn(Contractor $c) => $c->name, ContractorResource::class),

            BelongsTo::make('Счёт', 'invoice', fn(Invoice $i) => "№{$i->number}", InvoiceResource::class),

            Text::make('Номер', 'number')->sortable(),

            Date::make('Дата', 'date')->format('d.m.Y')->sortable(),

            Enum::make('Статус', 'status')->attach(ActStatus::class),

            Text::make('Итого', 'total'),
        ];
    }

    protected function filters(): iterable
    {
        return [
            Text::make('Номер', 'number'),
            Enum::make('Статус', 'status')->attach(ActStatus::class),
        ];
    }

    protected function modifyListComponent(ComponentContract $component): TableBuilder
    {
        return $component->columnSelection();
    }
}
