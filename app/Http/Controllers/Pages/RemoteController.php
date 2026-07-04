<?php

namespace App\Http\Controllers\Pages;

use App\Enums\Pages\PageTemplate;
use App\Enums\Resources\TeaserTemplate;
use Domain\Remote\ViewModels\RemoteViewModel;
use Illuminate\Contracts\View\View;

class RemoteController extends PageController
{
    public function index(): View
    {
        $vm    = RemoteViewModel::make();
        $items = $vm->getPublished();
        $page  = $vm->getPageData();

        return view('pages.remote.list', [
            'page'            => $page,
            'items'           => $items,
            'pageSuffix'      => $this->pageSuffix($items),
            'template'        => PageTemplate::from($page->page_template ?? PageTemplate::Default->value),
            'teaser_template' => TeaserTemplate::from($page->section_template ?? TeaserTemplate::Default->value),
            'section'         => 'remote',
            'route'           => 'remote.show',
        ]);
    }

    public function indexShow(string $slug): View
    {
        $vm   = RemoteViewModel::make();
        $item = $vm->getBySlug($slug);
        $page = $vm->getPageData();

        return view('pages.remote.show', [
            'page'     => $page,
            'item'     => $item,
            'resource' => 'remote',
        ]);
    }
}
