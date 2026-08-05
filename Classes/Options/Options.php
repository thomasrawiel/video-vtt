<?php
declare(strict_types=1);

namespace TRAW\VideoVtt\Options;

use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;

class Options
{
    protected array $options;
    protected FileInterface $file;

    public function __construct(FileInterface $file, array $options = [])
    {
        $this->file = $file;
        $options['autoplay'] = (bool)$file->getProperty('autoplay');
        $options['mute'] = (bool)$file->getProperty('mute');
        $options['loop'] = (bool)$file->getProperty('loop');
        $options['showinfo'] = (bool)$file->getProperty('showinfo');
        $options['controls'] = (bool)$file->getProperty('controls');
        $options['controlsList'] = (int)$file->getProperty('controlslist');
        $options['picinpic'] = (bool)$file->getProperty('picinpic');
        $options['lang'] = (string)$file->getProperty('lang');
        $options['start_time'] = (int)$file->getProperty('start_time');
        $options['end_time'] = (int)$file->getProperty('end_time');

        $options['no-cookie'] = true;

        $this->options = $options;
    }

    public function toArray(): array
    {
        return $this->options;
    }

    public function get(string $key): mixed
    {
        return $this->options[$key] ?? null;
    }

    public function set(string $key, mixed $value)
    {
        $this->options[$key] = $value;
    }

    public function getAutoPlay(): bool
    {
        return (bool)($this->options['autoplay'] ?? false);
    }

    public function getMute(): bool
    {
        return (bool)($this->options['mute'] ?? false);
    }

    public function getLoop(): bool
    {
        return (bool)($this->options['loop'] ?? false);
    }

    public function getShowInfo(): bool
    {
        return (bool)($this->options['showinfo'] ?? false);
    }

    public function getControls(): bool
    {
        return (bool)($this->options['controls'] ?? false);
    }

    public function getControlsList(): int
    {
        return $this->options['controlsList'] ?? 0;
    }

    public function getControlsListValue(): string
    {
        if ($this->file->getOriginalFile()->getType() === \TYPO3\CMS\Core\Resource\AbstractFile::FILETYPE_VIDEO) {
            return $this->getControlsListValueVideo();
        }
        if ($this->file->getOriginalFile()->getType() === \TYPO3\CMS\Core\Resource\AbstractFile::FILETYPE_AUDIO) {
            return $this->getControlsListValueAudio();
        }
        return '';
    }

    public function getControlsListValueVideo(): string
    {
        $controlsList = [
            1 => 'nodownload',
            2 => 'noplaybackrate',
            4 => 'nofullscreen',
            8 => 'noremoteplayback',
            3 => 'nodownload noplaybackrate',
            5 => 'nodownload nofullscreen',
            9 => 'nodownload noremoteplayback',
            6 => 'noplaybackrate nofullscreen',
            10 => 'noplaybackrate noremoteplayback',
            12 => 'nofullscreen noremoteplayback',
            7 => 'nodownload noplaybackrate nofullscreen',
            11 => 'nodownload noplaybackrate noremoteplayback',
            13 => 'nodownload nofullscreen noremoteplayback',
            14 => 'noplaybackrate nofullscreen noremoteplayback',
            15 => 'nodownload noplaybackrate nofullscreen noremoteplayback',
        ];
        if (in_array($this->file->getOriginalFile()->getExtension(), ['youtube', 'vimeo'])) {
            return $this->getControlsList() ? 'nofullscreen' : '';
        }

        return $controlsList[$this->getControlsList()] ?? '';
    }

    public function getControlsListValueAudio(): string
    {
        $controlsList = [
            1 => 'nodownload',
            2 => 'noplaybackrate',
            3 => 'nodownload noplaybackrate',
        ];

        return $controlsList[$this->getControlsList()] ?? '';
    }


    public function getPicinpic(): bool
    {
        return (bool)($this->options['picinpic'] ?? false);
    }

    public function getLang(): string
    {
        return $this->options['lang'] ?? '';
    }

    public function getStartTime(): int
    {
        return $this->options['start_time'] ?? 0;
    }

    public function getEndTime(): int
    {
        return $this->options['end_time'] ?? 0;
    }

    public function getData(): array
    {
        return $this->options['data'] ?? [];
    }

    public function getAdditionalAttributes(): array
    {
        return $this->options['additionalAttributes'] ?? [];
    }

    public function getAdditionalConfig(): array
    {
        return $this->options['additionalConfig'] ?? [];
    }
}
