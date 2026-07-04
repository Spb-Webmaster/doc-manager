<?php

namespace Domain\About\ViewModels;

use App\Models\Document;
use App\Models\Setting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Fluent;
use Support\Traits\Makeable;

class DocumentViewModel
{
    use Makeable;

    public function getPageData(): Fluent
    {
        return new Fluent(Setting::getGroup('onas_dokumenty')->data ?? []);
    }

    public function getPublished(): LengthAwarePaginator
    {
        return Document::published()->paginate(config('site.constants.paginate'));
    }

    public function getBySlug(string $slug): Document
    {
        return Document::published()->where('slug', $slug)->firstOrFail();
    }
}
