<?php
declare(strict_types=1);

namespace TRAW\VideoVtt\Utility;

use TRAW\VideoVtt\Options\Options;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AttributeUtility
{
    protected ?Options $options = null;

    protected array $excludeAttributes = ['api', 'no-cookie'];

    public function getAudioAttributes(FileInterface $file, array $options = []): array
    {
        if ($this->options === null) {
            $this->options = new Options($file, $options);
        }
        $attributes = $this->getGenericMediaAttributes($file, $options);

        return array_unique($attributes);
    }

    public function getVideoAttributes(FileInterface $file, int $width, int $height, array $options = []): array
    {
        if ($this->options === null) {
            $this->options = new Options($file, $options);
        }

        $attributes = $this->getGenericMediaAttributes($file, $options);

        if ($width > 0) {
            $attributes[] = 'width="' . (int)$width . '"';
        }

        if ($height > 0) {
            $attributes[] = 'height="' . (int)$height . '"';
        }

        if ($this->options->getAutoPlay()) {
            $attributes[] = 'playsinline';
        }

        if (!$this->options->getPicinpic()) {
            $attributes[] = 'disablePictureInPicture';
        }

        return array_unique($attributes);
    }

    protected function getGenericMediaAttributes(FileInterface $file, array $options = []): array
    {
        if ($this->options === null) {
            $this->options = new Options($file, $options);
        }

        if ($this->options->getAdditionalAttributes() !== []) {
            $attributes[] = GeneralUtility::implodeAttributes($this->options->getAdditionalAttributes(), true, true);
        }
        if ($this->options->getData() !== []) {
            $data = $this->options->getData();
            array_walk($data, static function (string &$value, string $key): void {
                $value = 'data-' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
            });
            $attributes[] = implode(' ', $data);
        }
        if ($this->options->getControls()) {
            $attributes[] = 'controls';
        }
        if ($this->options->getAutoPlay()) {
            $attributes[] = 'autoplay';
            $attributes[] = 'muted';
        }
        if ($this->options->getMute() && !$this->options->getAutoPlay()) {
            $attributes[] = 'muted';
        }
        if ($this->options->getLoop()) {
            $attributes[] = 'loop';
        }
        if ($this->options->getControlsList()) {
            $controlsList = $this->options->getControlsListValueAudio();
            $attributes[] = 'controlsList="' . htmlspecialchars($controlsList) . '"';
        }

        if ($this->options->getAdditionalConfig() !== []) {
            foreach ($this->options->getAdditionalConfig() as $key => $value) {
                if ($value && !in_array($key, $this->excludeAttributes, true)) {
                    if ((int)$value !== 1) {
                        $attributes[] = htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
                    } else {
                        $attributes[] = htmlspecialchars($key);
                    }
                    // Ensure that the property is not set afterwards
                    $this->options->set($key, false);
                }
            }
        }

        foreach (['class', 'dir', 'id', 'lang', 'style', 'title', 'accesskey', 'tabindex', 'onclick', 'preload'] as $key) {
            if (!empty($this->options->get($key))) {
                $attributes[] = $key . '="' . htmlspecialchars((string)$this->options->get($key)) . '"';
            }
        }

        return array_unique($attributes);
    }

    public function getSourceTime(FileInterface $file, array $options): string
    {
        if ($this->options === null) {
            $this->options = new Options($file, $options);
        }

        $start = $this->options->getStartTime();
        if ($start < 0) {
            $start = 0;
        }
        $times = [$start];

        $end = $this->options->getEndTime();
        if ($end > $start) {
            $times[] = $end;
        }
        if ($start !== 0 || $end !== 0) {
            return sprintf('#t=%s', implode(',', $times));
        }

        return '';
    }
}
