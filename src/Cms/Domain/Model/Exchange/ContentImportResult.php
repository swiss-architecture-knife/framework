<?php

namespace Swark\Cms\Domain\Model\Exchange;

readonly class ContentImportResult
{
    public function __construct(
        public string              $localPath,
        public ContentImportStatus $status,
        public ?string             $message = null,
        public ?string             $tag = null)
    {
    }
}
