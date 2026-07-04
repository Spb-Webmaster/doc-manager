<?php

declare(strict_types=1);

namespace App\Providers;

use App\MoonShine\Pages\Pages\HomePage;
use Illuminate\Support\ServiceProvider;
use App\MoonShine\Resources\City\CityResource;
use App\MoonShine\Resources\Document\DocumentResource;
use App\MoonShine\Resources\News\NewsResource;
use App\MoonShine\Resources\MoonShineUser\MoonShineUserResource;
use App\MoonShine\Resources\MoonShineUserRole\MoonShineUserRoleResource;
use App\MoonShine\Resources\User\UserResource;
use App\MoonShine\Resources\LegalEntity\LegalEntityResource;
use App\MoonShine\Resources\IndividualEntrepreneur\IndividualEntrepreneurResource;
use App\MoonShine\Resources\SelfEmployed\SelfEmployedResource;
use App\MoonShine\Resources\Contractor\ContractorResource;
use App\MoonShine\Resources\Invoice\InvoiceResource;
use App\MoonShine\Resources\Invoice\InvoiceItemResource;
use App\MoonShine\Resources\Act\ActResource;
use App\MoonShine\Resources\Act\ActItemResource;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;

class MoonShineServiceProvider extends ServiceProvider
{
    /**
     * @param  CoreContract<MoonShineConfigurator>  $core
     */
    public function boot(CoreContract $core): void
    {
        $core
            ->resources([
                CityResource::class,
                DocumentResource::class,
                NewsResource::class,
                MoonShineUserResource::class,
                MoonShineUserRoleResource::class,
                UserResource::class,
                LegalEntityResource::class,
                IndividualEntrepreneurResource::class,
                SelfEmployedResource::class,
                ContractorResource::class,
                InvoiceResource::class,
                InvoiceItemResource::class,
                ActResource::class,
                ActItemResource::class,
            ])
            ->pages([
                ...$core->getConfig()->getPages(),
                HomePage::class,
            ])
        ;
    }
}
