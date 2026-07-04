<?php

namespace Domain\About\ViewModels;

use App\Models\About;
use App\Models\Setting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Fluent;
use Support\Traits\Makeable;

class AboutViewModel
{
    use Makeable;

    public function getPageData(): Fluent
    {
        return new Fluent(Setting::getGroup('onas')->data ?? []);
    }

    public function getPublished(): LengthAwarePaginator
    {
        return About::published()->paginate(config('site.constants.paginate'));
    }

    public function getBySlug(string $slug): About
    {
        return About::published()->where('slug', $slug)->firstOrFail();
    }
}
