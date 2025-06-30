<?php

namespace App\Enums;

enum PlatformsEnum: string
{

    case ANDROID = 'Android';

    case LINUX = 'Linux';

    case MAC_OS = 'Mac OS';

    case UNKNOWN = 'Unknown';

    case WINDOWS = 'Windows';

    public function toString(): ?string
    {
        return match ($this) {
            self::ANDROID => 'Android',
            self::LINUX => 'Linux',
            self::MAC_OS => 'Mac OS',
            self::UNKNOWN => 'Unknown',
            self::WINDOWS => 'Windows',
        };
    }
}
