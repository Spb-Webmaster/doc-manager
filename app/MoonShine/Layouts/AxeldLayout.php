<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use App\Models\Document;
use App\MoonShine\Pages\Pages\HomePage;
use App\MoonShine\Resources\City\CityResource;
use App\MoonShine\Resources\Document\DocumentResource;
use App\MoonShine\Resources\MoonShineUser\MoonShineUserResource;
use App\MoonShine\Resources\User\UserResource;
use App\MoonShine\Resources\LegalEntity\LegalEntityResource;
use App\MoonShine\Resources\IndividualEntrepreneur\IndividualEntrepreneurResource;
use App\MoonShine\Resources\SelfEmployed\SelfEmployedResource;
use App\MoonShine\Resources\Contractor\ContractorResource;
use App\MoonShine\Resources\Invoice\InvoiceResource;
use App\MoonShine\Resources\Act\ActResource;
use App\MoonShine\Resources\News\NewsResource;
use MoonShine\AssetManager\Js;
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\ColorManager\Palettes\PurplePalette;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Contracts\ColorManager\PaletteContract;
use MoonShine\MenuManager\MenuDivider;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem;
use YuriZoom\MoonShineMediaManager\Pages\MediaManagerPage;


final class AxeldLayout extends AppLayout
{
    /**
     * @var null|class-string<PaletteContract>
     */
    protected ?string $palette = PurplePalette::class;

    protected function assets(): array
    {
        return [
            ...parent::assets(),
            new Js('/js/admin/tab-persist.js'),
        ];
    }

    protected function menu(): array
    {
        return [
            MenuGroup::make('Пользователи', [
                MenuItem::make(UserResource::class, 'Пользователи', 'users'),
                MenuItem::make(MoonShineUserResource::class, 'Админ', 'user'),
                MenuDivider::make(),
            ]),

            MenuGroup::make('Реквизиты', [
                MenuItem::make(LegalEntityResource::class, 'Юр. лица', 'building-office'),
                MenuItem::make(IndividualEntrepreneurResource::class, 'ИП', 'briefcase'),
                MenuItem::make(SelfEmployedResource::class, 'Самозанятые', 'user-circle'),
                MenuDivider::make(),
            ]),

            MenuGroup::make('Документы', [
                MenuItem::make(ContractorResource::class, 'Контрагенты', 'building-storefront'),
                MenuDivider::make(),
                MenuItem::make(InvoiceResource::class, 'Счета', 'document-text'),
                MenuItem::make(ActResource::class, 'Акты', 'clipboard-document-check'),
                MenuDivider::make(),
            ]),

            MenuGroup::make(static fn() => __('Страницы'), [
                MenuItem::make(HomePage::class, 'Главная', 'home'),
                MenuDivider::make(),
            ]),

            MenuGroup::make(static fn() => __('Настройки'), [
                MenuItem::make(CityResource::class, 'Города', 'building-office-2'),
                MenuItem::make(MediaManagerPage::class, 'Media', 'film'),
                ]),
        ];
    }

    /**
     * @param ColorManager $colorManager
     */
    protected function colors(ColorManagerContract $colorManager): void
    {
        parent::colors($colorManager);

        // $colorManager->primary('#00000');
    }

    protected function getFooterCopyright(): string
    {
        return \sprintf(
            <<<'HTML'
                &copy; %d Портал
                <a href="/"
                    class="font-semibold text-primary"
                    target="_blank"
                >
                    СчетOK
                </a>
                HTML,
            now()->year,
        );
    }

    protected function getFooterMenu(): array
    {
        return [
            config('app.url') => 'WebSite',
        ];
    }
}
