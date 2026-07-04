<?php

namespace App\Http\Controllers\Pages;

use App\Enums\Pages\PageTemplate;
use App\Enums\Resources\TeaserTemplate;
use Domain\Consulting\ViewModels\ConsultingViewModel;
use Illuminate\Contracts\View\View;

class ConsultingController extends PageController
{
    public function index(): View
    {
        $vm    = ConsultingViewModel::make();
        $items = $vm->getPublished();
        $page  = $vm->getPageData();

        return view('pages.consulting.list', [
            'page'            => $page,
            'items'           => $items,
            'pageSuffix'      => $this->pageSuffix($items),
            'template'        => PageTemplate::from($page->page_template ?? PageTemplate::Default->value),
            'teaser_template' => TeaserTemplate::from($page->section_template ?? TeaserTemplate::Default->value),
            'section'         => 'consulting',
            'route'           => 'consulting.show',
        ]);
    }

    public function indexShow(string $slug): View
    {
        $vm   = ConsultingViewModel::make();
        $item = $vm->getBySlug($slug);
        $page = $vm->getPageData();

        return view('pages.consulting.show', [
            'page'     => $page,
            'item'     => $item,
            'resource' => 'consulting',
        ]);
    }
}
