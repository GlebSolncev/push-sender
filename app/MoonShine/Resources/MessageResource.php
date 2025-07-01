<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Enums\CountriesEnum;
use App\Enums\PlatformsEnum;
use App\Jobs\SendPushNotification;
use App\Models\Message;
use App\Models\Subscriber;
use App\Services\TestPushNotification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Url;

/**
 * @extends ModelResource<Message>
 */
class MessageResource extends ModelResource
{
    protected string $model = Message::class;

    protected string $title = 'Messages';
    
    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),

            Text::make('title'),
            Text::make('body'),
            Url::make('link'),
            Image::make('icon')->disk('public'),
            Image::make('image')->disk('public'),

            Enum::make('platform')->attach(PlatformsEnum::class),
            Enum::make('country')->attach(CountriesEnum::class),

            Text::make('subscribers', '', function(Message $message){
                return $message->countSubscribers();
            }),
        ];
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function formFields(): iterable
    {
        return [
            Box::make([
                ID::make(),

                Text::make('title'),
                Text::make('body'),
                Url::make('link'),
                Image::make('icon')->disk('public'),
                Image::make('image')->disk('public'),

                Enum::make('platform')->attach(PlatformsEnum::class)->searchable(),
                Enum::make('country')->attach(CountriesEnum::class)->searchable(),
            ])
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return [
            ID::make(),
        ];
    }

    /**
     * @param Message $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [];
    }


    protected function afterCreated(mixed $item): mixed
    {
        SendPushNotification::dispatch($item)->onQueue('send-push-notification');

        return $item;
    }

    protected function afterUpdated(mixed $item): mixed
    {
        SendPushNotification::dispatch($item)->onQueue('send-push-notification');

        return $item;
    }

    protected function formBuilderButtons(): ListOf
    {
        return parent::formBuilderButtons()
            ->add(
                ActionButton::make('Test', '/api/push/test/' . $this->item->id)
                    ->async()
                    ->inModal(
                        title: fn() => 'Test push notification example',
                    )
            );
    }
}
