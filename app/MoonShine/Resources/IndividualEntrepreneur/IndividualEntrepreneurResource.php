<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\IndividualEntrepreneur;

use App\Models\IndividualEntrepreneur;
use App\MoonShine\Resources\IndividualEntrepreneur\Pages\IndividualEntrepreneurFormPage;
use App\MoonShine\Resources\IndividualEntrepreneur\Pages\IndividualEntrepreneurIndexPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;

/**
 * @extends ModelResource<IndividualEntrepreneur, IndividualEntrepreneurIndexPage, IndividualEntrepreneurFormPage, null>
 */
#[Icon('briefcase')]
#[Order(3)]
class IndividualEntrepreneurResource extends ModelResource
{
    protected string $model = IndividualEntrepreneur::class;

    protected string $column = 'full_name';

    protected bool $simplePaginate = true;

    public function getTitle(): string
    {
        return 'Индивидуальные предприниматели';
    }

    protected function activeActions(): ListOf
    {
        return parent::activeActions()->except(Action::VIEW);
    }

    protected function pages(): array
    {
        return [
            IndividualEntrepreneurIndexPage::class,
            IndividualEntrepreneurFormPage::class,
        ];
    }

    protected function search(): array
    {
        return ['id', 'full_name', 'inn', 'email'];
    }
}
