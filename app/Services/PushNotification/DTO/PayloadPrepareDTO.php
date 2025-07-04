<?php

namespace App\Services\PushNotification\DTO;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Storage;

class PayloadPrepareDTO implements Arrayable
{
    private string $icon = '';
    private string $image = '';
    public function __construct(
        public readonly string      $title,
        public readonly string      $body,
        public readonly array       $data,
    ) {}

    public function setIcon(string $value): void
    {
        if($value !== '') {
            $this->icon = Storage::disk('public')->url($value);
        }
    }

    public function setImage(string $value): void
    {
        if($value !== '') {
            $this->image = Storage::disk('public')->url($value);
        }
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'icon'  => $this->icon,
            'image' => $this->image,
            'body'  => $this->body,
            'data'  => $this->data
        ];
    }
}