<?php

namespace Domain\Resources\ViewModels;

use App\Models\Seminar;
use App\Models\Setting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Fluent;
use Support\Traits\Makeable;

class SeminarViewModel
{
    use Makeable;

    public function getPageData(): Fluent
    {
        return new Fluent(Setting::getGroup('poleznoe_seminar')->data ?? []);
    }

    public function getPublished(): LengthAwarePaginator
    {
        return Seminar::published()->paginate(config('site.constants.paginate'));
    }

    public function getBySlug(string $slug): Seminar
    {
        return Seminar::published()->where('slug', $slug)->firstOrFail();
    }
}
