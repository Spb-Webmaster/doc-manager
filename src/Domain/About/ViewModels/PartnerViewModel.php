<?php

namespace Domain\About\ViewModels;

use App\Models\Partner;
use App\Models\Setting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Fluent;
use Support\Traits\Makeable;

class PartnerViewModel
{
    use Makeable;

    public function getPageData(): Fluent
    {
        return new Fluent(Setting::getGroup('onas_partnjory')->data ?? []);
    }

    public function getPublished(): LengthAwarePaginator
    {
        return Partner::published()->paginate(config('site.constants.paginate'));
    }

    public function getBySlug(string $slug): Partner
    {
        return Partner::published()->where('slug', $slug)->firstOrFail();
    }
}
