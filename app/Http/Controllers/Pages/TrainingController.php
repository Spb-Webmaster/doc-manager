<?php

namespace App\Http\Controllers\Pages;

use App\Enums\Pages\PageTemplate;
use App\Enums\Resources\TeaserTemplate;
use App\Models\TrainingCategory;
use Domain\Training\ViewModels\TrainingViewModel;
use Illuminate\Contracts\View\View;

class TrainingController extends PageController
{
    public function index(): View
    {
        $vm    = TrainingViewModel::make();
        $items = $vm->getPublished();
        $page  = $vm->getPageData();

        $categories = TrainingCategory::orderBy('sorting')->get();

        return view('pages.training.list', [
            'page'            => $page,
            'items'           => $items,
            'categories'      => $categories,
            'pageSuffix'      => $this->pageSuffix($items),
            'template'        => PageTemplate::from($page->page_template ?? PageTemplate::Default->value),
            'teaser_template' => TeaserTemplate::from($page->section_template ?? TeaserTemplate::Default->value),
            'section'         => 'training',
            'route'           => 'training.show',
        ]);
    }

    public function indexShow(string $slug): View
    {
        $vm   = TrainingViewModel::make();
        $item = $vm->getBySlug($slug);
        $page = $vm->getPageData();

        return view('pages.training.show', [
            'page'     => $page,
            'item'     => $item,
            'resource' => 'training',
        ]);
    }
}
