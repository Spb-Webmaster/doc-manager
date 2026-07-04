<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\LegalEntity;

use App\Models\LegalEntity;
use App\MoonShine\Resources\LegalEntity\Pages\LegalEntityFormPage;
use App\MoonShine\Resources\LegalEntity\Pages\LegalEntityIndexPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;

/**
 * @extends ModelResource<LegalEntity, LegalEntityIndexPage, LegalEntityFormPage, null>
 */
#[Icon('building-office')]
#[Order(2)]
class LegalEntityResource extends ModelResource
{
    protected string $model = LegalEntity::class;

    protected string $column = 'name';

    protected bool $simplePaginate = true;

    public function getTitle(): string
    {
        return 'Юридические лица';
    }

    protected function activeActions(): ListOf
    {
        return parent::activeActions()->except(Action::VIEW);
    }

    protected function pages(): array
    {
        return [
            LegalEntityIndexPage::class,
            LegalEntityFormPage::class,
        ];
    }

    protected function search(): array
    {
        return ['id', 'name', 'inn', 'email'];
    }
}
