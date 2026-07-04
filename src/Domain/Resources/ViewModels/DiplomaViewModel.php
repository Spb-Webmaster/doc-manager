<?php

namespace Domain\Resources\ViewModels;

use App\Models\Diploma;
use App\Models\Setting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Fluent;
use Support\Traits\Makeable;

class DiplomaViewModel
{
    use Makeable;

    public function getPageData(): Fluent
    {
        return new Fluent(Setting::getGroup('poleznoe_diplomy')->data ?? []);
    }

    public function getPublished(): LengthAwarePaginator
    {
        return Diploma::published()->paginate(config('site.constants.paginate'));
    }

    public function getBySlug(string $slug): Diploma
    {
        return Diploma::published()->where('slug', $slug)->firstOrFail();
    }
}
