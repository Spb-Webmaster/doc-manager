<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Invoice\Pages;

use App\Enums\InvoiceStatus;
use App\Models\Contractor;
use App\Models\User;
use App\MoonShine\Resources\Contractor\ContractorResource;
use App\MoonShine\Resources\Invoice\InvoiceItemResource;
use App\MoonShine\Resources\Invoice\InvoiceResource;
use App\MoonShine\Resources\User\UserResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\RelationRepeater;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

/**
 * @extends FormPage<InvoiceResource>
 */
final class InvoiceFormPage extends FormPage
{
    protected function fields(): iterable
    {
        return [
            Box::make([
                ID::make(),

                Flex::make([
                    BelongsTo::make('Пользователь', 'user', fn(User $u) => "{$u->name} ({$u->email})", UserResource::class)
                        ->required()
                        ->searchable(),

                    BelongsTo::make('Контрагент', 'contractor', fn(Contractor $c) => $c->name, ContractorResource::class)
                        ->required()
                        ->searchable(),
                ]),

                Flex::make([
                    Text::make('Номер', 'number')->required(),
                    Date::make('Дата', 'date')->required(),
                    Date::make('Срок оплаты', 'due_date'),
                    Enum::make('Статус', 'status')
                        ->attach(InvoiceStatus::class)
                        ->required(),
                ]),
            ]),

            Box::make('Позиции', [
                RelationRepeater::make('Позиции', 'items', resource: InvoiceItemResource::class),
            ]),

            Box::make('Итоги и примечание', [
                Flex::make([
                    Number::make('Сумма без НДС', 'subtotal')->step(0.01),
                    Number::make('Сумма НДС', 'nds_amount')->step(0.01),
                    Number::make('Итого', 'total')->step(0.01),
                ]),

                Textarea::make('Примечание', 'comment'),
            ]),
        ];
    }

    protected function rules(mixed $item): array
    {
        return [
            'user_id'       => ['required', 'integer', 'exists:users,id'],
            'contractor_id' => ['required', 'integer', 'exists:contractors,id'],
            'number'        => ['required', 'string', 'max:50'],
            'date'          => ['required', 'date'],
            'due_date'      => ['nullable', 'date'],
            'status'        => ['required', 'string'],
            'subtotal'      => ['nullable', 'numeric', 'min:0'],
            'nds_amount'    => ['nullable', 'numeric', 'min:0'],
            'total'         => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
