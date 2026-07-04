<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Document;

use App\Models\Document;
use App\MoonShine\Resources\Document\Pages\DocumentDetailPage;
use App\MoonShine\Resources\Document\Pages\DocumentFormPage;
use App\MoonShine\Resources\Document\Pages\DocumentIndexPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;

/**
 * @extends ModelResource<Document, DocumentIndexPage, DocumentFormPage>
 */
#[Icon('document')]
#[Group('Контент', 'document-text')]
#[Order(11)]
class DocumentResource extends ModelResource
{
    protected string $model = Document::class;

    protected string $column = 'title';
    protected string $sortColumn = 'sorting';
    protected bool $simplePaginate = true;

    public function getTitle(): string
    {
        return 'Документы';
    }

    protected function pages(): array
    {
        return [
            DocumentIndexPage::class,
            DocumentFormPage::class,
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'title',
            'slug',
        ];
    }
}
