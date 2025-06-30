<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Subscriber;

use App\Models\Subscriber;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

/**
 * @extends ModelResource<Subscriber>
 */
class SubscriberResource extends ModelResource
{
    protected string $model = Subscriber::class;

    protected string $title = 'Subscribers';

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('platform'),
            Text::make('country'),
            Text::make('geo')
        ];
    }

    /**
     * @return FieldContract
     */
    protected function formFields(): iterable
    {
        return [
            Box::make([
                ID::make()->sortable(),
                Text::make('platform'),
                Text::make('country'),
                Text::make('auth_token'),
                Text::make('public_key'),
                Text::make('endpoint'),
                Text::make('geo'),
            ])
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('platform'),
            Text::make('country'),
            Text::make('geo')
        ];
    }

    /**
     * @param Subscriber $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [];
    }
}
