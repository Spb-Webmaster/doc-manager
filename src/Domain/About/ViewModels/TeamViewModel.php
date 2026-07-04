<?php

namespace Domain\About\ViewModels;

use App\Models\Team;
use App\Models\Setting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Fluent;
use Support\Traits\Makeable;

class TeamViewModel
{
    use Makeable;

    public function getPageData(): Fluent
    {
        return new Fluent(Setting::getGroup('onas_team')->data ?? []);
    }

    public function getPublished(): LengthAwarePaginator
    {
        return Team::published()->paginate(config('site.constants.paginate'));
    }

    public function getBySlug(string $slug): Team
    {
        return Team::published()->where('slug', $slug)->firstOrFail();
    }
}
