<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Act;

use App\Models\Act;
use App\MoonShine\Resources\Act\Pages\ActFormPage;
use App\MoonShine\Resources\Act\Pages\ActIndexPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;

/**
 * @extends ModelResource<Act, ActIndexPage, ActFormPage, null>
 */
#[Icon('clipboard-document-check')]
#[Order(7)]
class ActResource extends ModelResource
{
    protected string $model = Act::class;

    protected string $column = 'number';

    protected bool $simplePaginate = true;

    public function getTitle(): string
    {
        return 'Акты';
    }

    protected function activeActions(): ListOf
    {
        return parent::activeActions()->except(Action::VIEW);
    }

    protected function pages(): array
    {
        return [
            ActIndexPage::class,
            ActFormPage::class,
        ];
    }

    protected function search(): array
    {
        return ['id', 'number'];
    }
}
