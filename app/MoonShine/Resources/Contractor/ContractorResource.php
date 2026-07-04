<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Contractor;

use App\Models\Contractor;
use App\MoonShine\Resources\Contractor\Pages\ContractorFormPage;
use App\MoonShine\Resources\Contractor\Pages\ContractorIndexPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;

/**
 * @extends ModelResource<Contractor, ContractorIndexPage, ContractorFormPage, null>
 */
#[Icon('building-storefront')]
#[Order(5)]
class ContractorResource extends ModelResource
{
    protected string $model = Contractor::class;

    protected string $column = 'name';

    protected bool $simplePaginate = true;

    public function getTitle(): string
    {
        return 'Контрагенты';
    }

    protected function activeActions(): ListOf
    {
        return parent::activeActions()->except(Action::VIEW);
    }

    protected function pages(): array
    {
        return [
            ContractorIndexPage::class,
            ContractorFormPage::class,
        ];
    }

    protected function search(): array
    {
        return ['id', 'name', 'inn', 'email'];
    }
}
