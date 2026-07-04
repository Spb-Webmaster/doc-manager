<?php

namespace Domain\Remote\ViewModels;

use App\Models\Online;
use App\Models\Setting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Fluent;
use Support\Traits\Makeable;

class RemoteViewModel
{
    use Makeable;

    public function getPageData(): Fluent
    {
        return new Fluent(Setting::getGroup('distantcionno')->data ?? []);
    }

    public function getPublished(): LengthAwarePaginator
    {
        return Online::published()->paginate(config('site.constants.paginate'));
    }

    public function getBySlug(string $slug): Online
    {
        return Online::published()->where('slug', $slug)->firstOrFail();
    }
}
