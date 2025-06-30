<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Subscriber;

use App\Models\Subscriber;
use App\MoonShine\Resources\SubscriberByCountry;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Fields\Text;

/**
 * @extends ModelResource<Subscriber>
 */
class SubscriberByCountryResource extends ModelResource
{
    protected string $model = Subscriber::class;

    protected string $title = 'by countries';

    protected function modifyQueryBuilder(Builder $builder): Builder {
        return $builder
            ->select([
                DB::raw('max(id) as id'),
                'country',
                DB::raw('count(*) as count'),
            ])
            ->groupBy('country');
    }

    protected function pages(): array
    {
        return [
            IndexPage::class,
        ];
    }

    protected function indexButtons(): ListOf {
        return parent::indexButtons()
            ->except(fn(ActionButton $btn) => in_array($btn->getName(), ['resource-delete-button', 'mass-delete-modal']));
    }

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            Text::make('country'),
            Text::make('count'),
        ];
    }

    /**
     * @param SubscriberByCountry $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [];
    }
}
