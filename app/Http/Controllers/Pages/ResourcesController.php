<?php

namespace App\Http\Controllers\Pages;

use App\Enums\Pages\PageTemplate;
use App\Enums\Resources\TeaserTemplate;
use App\Models\Setting;
use Domain\Resources\ViewModels\DiplomaViewModel;
use Domain\Resources\ViewModels\ImportantViewModel;
use Domain\Resources\ViewModels\LawViewModel;
use Domain\Resources\ViewModels\NewsViewModel;
use Domain\Resources\ViewModels\SeminarViewModel;
use Domain\Resources\ViewModels\UsefulViewModel;
use Illuminate\Contracts\View\View;

class ResourcesController extends PageController
{
    private function tabs(): array
    {
        $map = [
            'poleznoe'         => 'resources',
            'poleznoe_zakony'  => 'resources.laws',
            'poleznoe_novosti' => 'resources.news',
            'poleznoe_vazhnoe' => 'resources.important',
            'poleznoe_diplomy' => 'resources.diplomas',
            'poleznoe_seminar' => 'resources.seminar',
        ];

        $settings = Setting::whereIn('group', array_keys($map))
            ->get()
            ->keyBy('group');

        return array_map(fn(string $group, string $route) => [
            'label' => $settings->get($group)?->getValue('menu_title')
                    ?: $settings->get($group)?->getValue('title')
                    ?: '',
            'route' => $route,
        ], array_keys($map), array_values($map));
    }

    public function index(): View
    {
        $vm    = UsefulViewModel::make();
        $items = $vm->getPublished();
        $page  = $vm->getPageData();

        return view('pages.resources.list', [
            'page'            => $page,
            'items'           => $items,
            'pageSuffix'      => $this->pageSuffix($items),
            'template'        => PageTemplate::from($page->page_template ?? PageTemplate::Default->value),
            'teaser_template' => TeaserTemplate::from($page->section_template ?? TeaserTemplate::Default->value),
            'section'         => 'resources',
            'route'           => 'resources.show',
            'tabs'            => $this->tabs(),
        ]);
    }

    public function indexShow(string $slug): View
    {
        $vm   = UsefulViewModel::make();
        $item = $vm->getBySlug($slug);
        $page = $vm->getPageData();

        return view('pages.resources.show', [
            'page'     => $page,
            'item'     => $item,
            'resource' => 'resources',
        ]);
    }

    public function news(): View
    {
        $vm    = NewsViewModel::make();
        $items = $vm->getPublished();
        $page  = $vm->getPageData();

        return view('pages.resources.list', [
            'page'            => $page,
            'items'           => $items,
            'pageSuffix'      => $this->pageSuffix($items),
            'template'        => PageTemplate::from($page->page_template ?? PageTemplate::Default->value),
            'teaser_template' => TeaserTemplate::from($page->section_template ?? TeaserTemplate::Default->value),
            'section'         => 'resources.news',
            'route'           => 'resources.news.show',
            'tabs'            => $this->tabs(),
        ]);
    }

    public function newsShow(string $slug): View
    {
        $vm   = NewsViewModel::make();
        $item = $vm->getBySlug($slug);
        $page = $vm->getPageData();

        return view('pages.resources.show', [
            'page'     => $page,
            'item'     => $item,
            'resource' => 'news',
        ]);
    }

    public function laws(): View
    {
        $vm    = LawViewModel::make();
        $items = $vm->getPublished();
        $page  = $vm->getPageData();

        return view('pages.resources.list', [
            'page'            => $page,
            'items'           => $items,
            'pageSuffix'      => $this->pageSuffix($items),
            'template'        => PageTemplate::from($page->page_template ?? PageTemplate::Default->value),
            'teaser_template' => TeaserTemplate::from($page->section_template ?? TeaserTemplate::Default->value),
            'section'         => 'resources.laws',
            'route'           => 'resources.laws.show',
            'tabs'            => $this->tabs(),
        ]);
    }

    public function lawsShow(string $slug): View
    {
        $vm   = LawViewModel::make();
        $item = $vm->getBySlug($slug);
        $page = $vm->getPageData();

        return view('pages.resources.show', [
            'page'     => $page,
            'item'     => $item,
            'resource' => 'laws',
        ]);
    }

    public function important(): View
    {
        $vm    = ImportantViewModel::make();
        $items = $vm->getPublished();
        $page  = $vm->getPageData();

        return view('pages.resources.list', [
            'page'            => $page,
            'items'           => $items,
            'pageSuffix'      => $this->pageSuffix($items),
            'template'        => PageTemplate::from($page->page_template ?? PageTemplate::Default->value),
            'teaser_template' => TeaserTemplate::from($page->section_template ?? TeaserTemplate::Default->value),
            'section'         => 'resources.important',
            'route'           => 'resources.important.show',
            'tabs'            => $this->tabs(),
        ]);
    }

    public function importantShow(string $slug): View
    {
        $vm   = ImportantViewModel::make();
        $item = $vm->getBySlug($slug);
        $page = $vm->getPageData();

        return view('pages.resources.show', [
            'page'     => $page,
            'item'     => $item,
            'resource' => 'important',
        ]);
    }

    public function diplomas(): View
    {
        $vm    = DiplomaViewModel::make();
        $items = $vm->getPublished();
        $page  = $vm->getPageData();

        return view('pages.resources.list', [
            'page'            => $page,
            'items'           => $items,
            'pageSuffix'      => $this->pageSuffix($items),
            'template'        => PageTemplate::from($page->page_template ?? PageTemplate::Default->value),
            'teaser_template' => TeaserTemplate::from($page->section_template ?? TeaserTemplate::Default->value),
            'section'         => 'resources.diplomas',
            'route'           => 'resources.diplomas.show',
            'tabs'            => $this->tabs(),
        ]);
    }

    public function diplomasShow(string $slug): View
    {
        $vm   = DiplomaViewModel::make();
        $item = $vm->getBySlug($slug);
        $page = $vm->getPageData();

        return view('pages.resources.show', [
            'page'     => $page,
            'item'     => $item,
            'resource' => 'diplomas',
        ]);
    }

    public function seminar(): View
    {
        $vm    = SeminarViewModel::make();
        $items = $vm->getPublished();
        $page  = $vm->getPageData();

        return view('pages.resources.list', [
            'page'            => $page,
            'items'           => $items,
            'pageSuffix'      => $this->pageSuffix($items),
            'template'        => PageTemplate::from($page->page_template ?? PageTemplate::Default->value),
            'teaser_template' => TeaserTemplate::from($page->section_template ?? TeaserTemplate::Default->value),
            'section'         => 'resources.seminar',
            'route'           => 'resources.seminar.show',
            'tabs'            => $this->tabs(),
        ]);
    }

    public function seminarShow(string $slug): View
    {
        $vm   = SeminarViewModel::make();
        $item = $vm->getBySlug($slug);
        $page = $vm->getPageData();

        return view('pages.resources.show', [
            'page'     => $page,
            'item'     => $item,
            'resource' => 'seminar',
        ]);
    }
}
