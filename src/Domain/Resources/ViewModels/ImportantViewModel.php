<?php

namespace Domain\Resources\ViewModels;

use App\Models\Important;
use App\Models\News;
use App\Models\Setting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Fluent;
use Support\Traits\Makeable;

class ImportantViewModel
{
    use Makeable;

    public function getPageData(): Fluent
    {
        return new Fluent(Setting::getGroup('poleznoe_vazhnoe')->data ?? []);
    }

    public function getPublished(): LengthAwarePaginator
    {
        return Important::published()->paginate(config('site.constants.paginate'));
    }

    public function getBySlug(string $slug): News
    {
        return News::published()->where('slug', $slug)->firstOrFail();
    }
}
