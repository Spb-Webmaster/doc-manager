<?php

namespace App\Http\Controllers\Pages;

use App\Enums\Pages\PageTemplate;
use App\Enums\Resources\TeaserTemplate;
use Domain\About\ViewModels\AboutViewModel;
use Domain\About\ViewModels\DocumentViewModel;
use Domain\About\ViewModels\PartnerViewModel;
use Domain\About\ViewModels\TeamViewModel;
use Illuminate\Contracts\View\View;

class AboutController extends PageController
{
    public function index(): View
    {
        $vm    = AboutViewModel::make();
        $items = $vm->getPublished();
        $page  = $vm->getPageData();

        return view('pages.about.list', [
            'page'            => $page,
            'items'           => $items,
            'pageSuffix'      => $this->pageSuffix($items),
            'template'        => PageTemplate::from($page->page_template ?? PageTemplate::Default->value),
            'teaser_template' => TeaserTemplate::from($page->section_template ?? TeaserTemplate::Default->value),
            'section'         => 'about',
            'route'           => 'about.show',
        ]);
    }

    public function indexShow(string $slug): View
    {
        $vm   = AboutViewModel::make();
        $item = $vm->getBySlug($slug);
        $page = $vm->getPageData();

        return view('pages.about.show', [
            'page'     => $page,
            'item'     => $item,
            'resource' => 'about',
        ]);
    }

    public function team(): View
    {
        $vm    = TeamViewModel::make();
        $items = $vm->getPublished();
        $page  = $vm->getPageData();

        return view('pages.about.list', [
            'page'            => $page,
            'items'           => $items,
            'pageSuffix'      => $this->pageSuffix($items),
            'template'        => PageTemplate::from($page->page_template ?? PageTemplate::Default->value),
            'teaser_template' => TeaserTemplate::from($page->section_template ?? TeaserTemplate::Default->value),
            'section'         => 'about.team',
            'route'           => 'about.team.show',
        ]);
    }

    public function teamShow(string $slug): View
    {
        $vm   = TeamViewModel::make();
        $item = $vm->getBySlug($slug);
        $page = $vm->getPageData();

        return view('pages.about.show', [
            'page'     => $page,
            'item'     => $item,
            'resource' => 'team',
        ]);
    }

    public function partners(): View
    {
        $vm    = PartnerViewModel::make();
        $items = $vm->getPublished();
        $page  = $vm->getPageData();

        return view('pages.about.list', [
            'page'            => $page,
            'items'           => $items,
            'pageSuffix'      => $this->pageSuffix($items),
            'template'        => PageTemplate::from($page->page_template ?? PageTemplate::Default->value),
            'teaser_template' => TeaserTemplate::from($page->section_template ?? TeaserTemplate::Default->value),
            'section'         => 'about.partners',
            'route'           => 'about.partners.show',
        ]);
    }

    public function partnersShow(string $slug): View
    {
        $vm   = PartnerViewModel::make();
        $item = $vm->getBySlug($slug);
        $page = $vm->getPageData();

        return view('pages.about.show', [
            'page'     => $page,
            'item'     => $item,
            'resource' => 'partners',
        ]);
    }

    public function documents(): View
    {
        $vm    = DocumentViewModel::make();
        $items = $vm->getPublished();
        $page  = $vm->getPageData();

        return view('pages.about.list', [
            'page'            => $page,
            'items'           => $items,
            'pageSuffix'      => $this->pageSuffix($items),
            'template'        => PageTemplate::from($page->page_template ?? PageTemplate::Default->value),
            'teaser_template' => TeaserTemplate::from($page->section_template ?? TeaserTemplate::Default->value),
            'section'         => 'about.documents',
            'route'           => 'about.documents.show',
        ]);
    }

    public function documentsShow(string $slug): View
    {
        $vm   = DocumentViewModel::make();
        $item = $vm->getBySlug($slug);
        $page = $vm->getPageData();

        return view('pages.about.show', [
            'page'     => $page,
            'item'     => $item,
            'resource' => 'documents',
        ]);
    }
}
