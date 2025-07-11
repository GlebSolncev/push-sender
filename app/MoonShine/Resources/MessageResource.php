<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Enums\CountriesEnum;
use App\Enums\PlatformsEnum;
use App\Enums\PrepareQueryTypesEnum;
use App\Enums\StatisticQueueStatusEnum;
use App\Jobs\PreparePushNotificationJob;
use App\Models\Message;
use App\Models\Statistic\StatisticQueue;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Http\Responses\MoonShineJsonResponse;
use MoonShine\Laravel\MoonShineRequest;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Enums\ToastType;
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
        $this->dispatchPush($item);

        return $item;
    }

    protected function afterUpdated(mixed $item): mixed
    {
        $this->dispatchPush($item);

        return $item;
    }

    private function dispatchPush(mixed $item): void
    {
        StatisticQueue::query()->insertOrIgnore(['message_id' => $item->id]);
        PreparePushNotificationJob::dispatch($item, PrepareQueryTypesEnum::REGULAR)
            ->onQueue('prepare-push-notification');
    }
    protected function formBuilderButtons(): ListOf
    {
        $buttons = parent::formBuilderButtons();

        if($this->item->id ?? null) {
            $buttons->add(
                ActionButton::make('Link')->method('previewPush', ['id' => $this->item->id])
            );
        }

        return $buttons;

    }


    public function previewPush(MoonShineRequest $request){
        $messageId = $request->get('id');

        $message = Message::query()->find($messageId);
        StatisticQueue::query()->insertOrIgnore(['message_id' => $messageId]);
        PreparePushNotificationJob::dispatch($message, PrepareQueryTypesEnum::PREVIEW)
            ->onQueue('prepare-push-notification');

        return MoonShineJsonResponse::make()->toast('Start preview push notification', ToastType::SUCCESS);

    }
}
