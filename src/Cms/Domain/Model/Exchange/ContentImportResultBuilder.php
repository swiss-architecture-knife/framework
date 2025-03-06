<?php

namespace Swark\Cms\Domain\Model\Exchange;

readonly class ContentImportResultBuilder
{
    public function __construct(public string $localPath)
    {
    }

    public static function of(string $localPath)
    {
        return new static($localPath);
    }

    public function make(ContentImportStatus $status, ?string $message = null, ?string $tag = null)
    {
        return new ContentImportResult($this->localPath, $status, $message, $tag);
    }
}
