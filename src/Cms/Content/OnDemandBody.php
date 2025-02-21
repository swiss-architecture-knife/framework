<?php

namespace Swark\Cms\Content;

use Swark\Cms\Body;
use Swark\Content\Domain\Model\ContentType;

class OnDemandBody extends Body
{
    private function __construct(public readonly string $path, ContentType $type)
    {
        parent::__construct('', $type);
    }

    private ?string $raw = null;

    public function raw(): string
    {
        if ($this->raw === null) {
            $this->raw = file_get_contents($this->path);
        }

        return $this->raw;
    }

    public static function ofFile(string $absolutePath, string $fileExtension)
    {
        $map = [
            'html' => ContentType::HTML,
            'htm' => ContentType::HTML,
            'md' => ContentType::MARKDOWN,
            'blade.php' => ContentType::BLADE,
            'yaml' => ContentType::YAML,
            'yml' => ContentType::YAML,
        ];

        throw_if(!isset($map[$fileExtension]), "invalid file extension $fileExtension provided for filesystem markup");

        return new static($absolutePath, $map[$fileExtension]);
    }
}
