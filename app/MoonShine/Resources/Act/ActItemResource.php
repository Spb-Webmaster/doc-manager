<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Act;

use App\Models\ActItem;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;

/**
 * @extends ModelResource<ActItem>
 */
class ActItemResource extends ModelResource
{
    protected string $model = ActItem::class;

    protected string $column = 'name';

    public function getTitle(): string
    {
        return 'Позиции акта';
    }

    protected function indexFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Наименование', 'name'),
        ];
    }

    protected function formFields(): iterable
    {
        return [
            Text::make('Наименование', 'name')->required(),
            Text::make('Ед. изм.', 'unit'),
            Number::make('Кол-во', 'quantity')->step(0.001),
            Number::make('Цена', 'price')->step(0.01),
            Number::make('Сумма', 'amount')->step(0.01),
            Number::make('НДС %', 'nds_rate'),
            Number::make('Сумма НДС', 'nds_amount')->step(0.01),
        ];
    }

    protected function detailFields(): iterable
    {
        return $this->formFields();
    }
}
