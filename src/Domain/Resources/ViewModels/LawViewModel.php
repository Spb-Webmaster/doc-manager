<?php

namespace Domain\Resources\ViewModels;

use App\Models\Law;
use App\Models\Setting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Fluent;
use Support\Traits\Makeable;

class LawViewModel
{
    use Makeable;

    public function getPageData(): Fluent
    {
        return new Fluent(Setting::getGroup('poleznoe_zakony')->data ?? []);
    }

    public function getPublished(): LengthAwarePaginator
    {
        return Law::published()->paginate(config('site.constants.paginate'));
    }

    public function getBySlug(string $slug): Law
    {
        return Law::published()->where('slug', $slug)->firstOrFail();
    }
}
