<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\SelfEmployed;

use App\Models\SelfEmployed;
use App\MoonShine\Resources\SelfEmployed\Pages\SelfEmployedFormPage;
use App\MoonShine\Resources\SelfEmployed\Pages\SelfEmployedIndexPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;

/**
 * @extends ModelResource<SelfEmployed, SelfEmployedIndexPage, SelfEmployedFormPage, null>
 */
#[Icon('user-circle')]
#[Order(4)]
class SelfEmployedResource extends ModelResource
{
    protected string $model = SelfEmployed::class;

    protected string $column = 'full_name';

    protected bool $simplePaginate = true;

    public function getTitle(): string
    {
        return 'Самозанятые';
    }

    protected function activeActions(): ListOf
    {
        return parent::activeActions()->except(Action::VIEW);
    }

    protected function pages(): array
    {
        return [
            SelfEmployedIndexPage::class,
            SelfEmployedFormPage::class,
        ];
    }

    protected function search(): array
    {
        return ['id', 'full_name', 'inn', 'email'];
    }
}
