<?php

declare(strict_types=1);

namespace TRAW\VideoVtt\Utility;

class DurationUtility
{
    public static function formatDuration(int $seconds): string
    {
        if ($seconds < 0) {
            throw new InvalidArgumentException('Seconds cannot be negative.', 6104770703);
        }

        $hours   = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $secs    = $seconds % 60;

        $parts = [];
        if ($hours > 0) {
            $parts[] = "{$hours}h";
        }
        if ($minutes > 0) {
            $parts[] = "{$minutes}m";
        }
        if ($secs > 0 || empty($parts)) {
            $parts[] = "{$secs}s";
        }

        return implode('', $parts);
    }
}
