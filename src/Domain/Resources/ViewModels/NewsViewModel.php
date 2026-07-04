<?php

namespace Domain\Resources\ViewModels;

use App\Models\News;
use App\Models\Setting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Fluent;
use Support\Traits\Makeable;

class NewsViewModel
{
    use Makeable;

    public function getPageData(): Fluent
    {
        return new Fluent(Setting::getGroup('poleznoe_novosti')->data ?? []);
    }

    public function getPublished(): LengthAwarePaginator
    {
        return News::published()->paginate(config('site.constants.paginate'));

    }

    public function getBySlug(string $slug): News
    {
        return News::published()->where('slug', $slug)->firstOrFail();
    }
}
