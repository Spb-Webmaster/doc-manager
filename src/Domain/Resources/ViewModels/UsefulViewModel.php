<?php

namespace Domain\Resources\ViewModels;

use App\Models\News;
use App\Models\Useful;
use App\Models\Setting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Fluent;
use Support\Traits\Makeable;

class UsefulViewModel
{
    use Makeable;

    public function getPageData(): Fluent
    {
        return new Fluent(Setting::getGroup('poleznoe')->data ?? []);
    }

    public function getPublished(): LengthAwarePaginator
    {
        return Useful::published()->paginate(config('site.constants.paginate'));
    }
    public function getBySlug(string $slug): News
    {
        return News::published()->where('slug', $slug)->firstOrFail();
    }
}
