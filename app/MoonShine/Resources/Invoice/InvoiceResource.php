<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Invoice;

use App\Models\Invoice;
use App\MoonShine\Resources\Invoice\Pages\InvoiceFormPage;
use App\MoonShine\Resources\Invoice\Pages\InvoiceIndexPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;

/**
 * @extends ModelResource<Invoice, InvoiceIndexPage, InvoiceFormPage, null>
 */
#[Icon('document-text')]
#[Order(6)]
class InvoiceResource extends ModelResource
{
    protected string $model = Invoice::class;

    protected string $column = 'number';

    protected bool $simplePaginate = true;

    public function getTitle(): string
    {
        return 'Счета';
    }

    protected function activeActions(): ListOf
    {
        return parent::activeActions()->except(Action::VIEW);
    }

    protected function pages(): array
    {
        return [
            InvoiceIndexPage::class,
            InvoiceFormPage::class,
        ];
    }

    protected function search(): array
    {
        return ['id', 'number'];
    }
}
