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
        $options['autoplay'] = $file->getProperty('autoplay');
        $options['mute'] = $file->getProperty('mute');
        $options['loop'] = $file->getProperty('loop');
        $options['showinfo'] = $file->getProperty('showinfo');
        $options['controls'] = $file->getProperty('controls');
        $options['controlsList'] = $file->getProperty('controlslist');
        $options['picinpic'] = $file->getProperty('picinpic');
        $options['lang'] = $file->getProperty('lang');
        $options['start_time'] = $file->getProperty('start_time');
        $options['end_time'] = $file->getProperty('end_time');

        $options['no-cookie'] = 1;

        $this->options = $options;
    }

    public function toArray(): array
    {
        return $this->options;
    }

    public function get(string $key)
    {
        return $this->options[$key] ?? null;
    }

    public function getAutoPlay(): int
    {
        return $this->options['autoplay'] ?? 0;
    }

    public function getMute(): int
    {
        return $this->options['mute'] ?? 0;
    }

    public function getLoop(): int
    {
        return $this->options['loop'] ?? 0;
    }

    public function getShowInfo(): int
    {
        return $this->options['showinfo'] ?? 0;
    }

    public function getControls(): int
    {
        return $this->options['controls'] ?? 0;
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


    public function getPicinpic(): int
    {
        return $this->options['picinpic'] ?? 0;
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
