<?php

declare(strict_types=1);

namespace TRAW\VideoVtt\Resource\DisplayCondition;

class TimeDisplayCondition extends AbstractDisplayCondition
{
    public function displayStartField(array $data): bool
    {
        $fileUid = $this->getFileUid($data);

        return $this->isYoutubeVideo($fileUid) || $this->isVimeoVideo($fileUid);
    }

    public function displayEndField(array $data): bool
    {
        $fileUid = $this->getFileUid($data);

        return $this->isYoutubeVideo($fileUid) || $this->isVimeoVideo($fileUid);
    }
}
