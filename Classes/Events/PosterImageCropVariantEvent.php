<?php
declare(strict_types=1);

namespace TRAW\VideoVtt\Events;

class PosterImageCropVariantEvent
{
    public function __construct(private string $cropVariant)
    {
    }

    public function getCropVariant(): string
    {
        return $this->cropVariant;
    }

    public function setCropVariant(string $cropVariant): void
    {
        $this->cropVariant = $cropVariant;
    }
}
