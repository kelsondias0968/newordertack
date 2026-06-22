<?php

namespace App\Enums;

enum Marketplace: string
{
    case Takealot = 'takealot';
    case Amazon   = 'amazon';
    case Worten   = 'worten';

    public function label(): string
    {
        return match ($this) {
            self::Takealot => 'Takealot',
            self::Amazon   => 'Amazon',
            self::Worten   => 'Worten',
        };
    }

    public function locale(): string
    {
        return match ($this) {
            self::Takealot => 'en',
            self::Amazon   => 'en',
            self::Worten   => 'pt',
        };
    }

    public function branding(): array
    {
        return match ($this) {
            self::Takealot => [
                'name'    => 'Takealot',
                'color'   => '#0b79bf',
                'logo'    => config('order_track.email_branding.logo_url'),
                'contact' => config('order_track.email_branding.contact'),
                'email'   => config('order_track.email_branding.email'),
                'address' => config('order_track.email_branding.address'),
            ],
            self::Amazon => [
                'name'    => 'Amazon',
                'color'   => '#FF9900',
                'logo'    => 'https://upload.wikimedia.org/wikipedia/commons/a/a9/Amazon_logo.svg',
                'contact' => 'Amazon Customer Service',
                'email'   => config('order_track.email_branding.email'),
                'address' => config('order_track.email_branding.address'),
            ],
            self::Worten => [
                'name'    => 'Worten',
                'color'   => '#E30613',
                'logo'    => 'https://i.postimg.cc/VkxV8N88/worten-desktop-Dl-N-JMO0.jpg',
                'contact' => 'Worten Apoio ao Cliente',
                'email'   => config('order_track.email_branding.email'),
                'address' => config('order_track.email_branding.address'),
            ],
        };
    }
}
