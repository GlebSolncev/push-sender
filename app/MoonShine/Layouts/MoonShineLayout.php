<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use App\MoonShine\Resources\Subscriber\SubscriberByCountryResource;
use App\MoonShine\Resources\Subscriber\SubscriberByPlatformsResource;
use App\MoonShine\Resources\Subscriber\SubscriberResource;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem;
use MoonShine\UI\Components\{Layout\Layout};
use App\MoonShine\Resources\MessageResource;

final class MoonShineLayout extends AppLayout
{
    protected function assets(): array
    {
        return [
            ...parent::assets(),
        ];
    }

    protected function menu(): array
    {
        return [
            ...parent::menu(),
            MenuGroup::make('Subscribers', [
                MenuItem::make('List', SubscriberResource::class),
                MenuGroup::make('Metric', [
                    MenuItem::make('By countries', SubscriberByCountryResource::class),
                    MenuItem::make('By platforms', SubscriberByPlatformsResource::class),
                ]),
            ]),
            MenuItem::make('Messages', MessageResource::class),
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

    public function build(): Layout
    {
        return parent::build();
    }
}
