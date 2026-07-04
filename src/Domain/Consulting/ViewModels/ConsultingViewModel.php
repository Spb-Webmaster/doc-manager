<?php

namespace Domain\Consulting\ViewModels;

use App\Models\Consulting;
use App\Models\Setting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Fluent;
use Support\Traits\Makeable;

class ConsultingViewModel
{
    use Makeable;

    public function getPageData(): Fluent
    {
        return new Fluent(Setting::getGroup('konsalting')->data ?? []);
    }

    public function getPublished(): LengthAwarePaginator
    {
        return Consulting::published()->paginate(config('site.constants.paginate'));
    }

    public function getBySlug(string $slug): Consulting
    {
        return Consulting::published()->where('slug', $slug)->firstOrFail();
    }
}
