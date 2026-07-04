<?php

namespace Domain\Training\ViewModels;

use App\Models\Training;
use App\Models\Setting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Fluent;
use Support\Traits\Makeable;

class TrainingViewModel
{
    use Makeable;

    public function getPageData(): Fluent
    {
        return new Fluent(Setting::getGroup('obuchenie')->data ?? []);
    }

    public function getPublished(): LengthAwarePaginator
    {
        return Training::with('categories')->published()->paginate(config('site.constants.paginate'));
    }

    public function getBySlug(string $slug): Training
    {
        return Training::published()->where('slug', $slug)->firstOrFail();
    }
}
