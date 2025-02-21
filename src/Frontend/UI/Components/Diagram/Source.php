<?php
declare(strict_types=1);

namespace Swark\Frontend\UI\Components\Diagram;

class Source
{
    public function __construct(public readonly string       $content,
                                private readonly Transformer $transformer,
                                public readonly ?string      $path = null,
                                public readonly ?string      $description = null,
    )
    {
    }

    private ?string $hash = null;
    private ?string $id = null;

    public function id(): string
    {
        if (!$this->id) {
            if ($this->path) {
                $this->id = sha1($this->path);
            } else {
                $this->id = $this->hash();
            }
        }

        return $this->id;
    }

    public function hash(): string
    {
        if (!$this->hash) {
            $this->hash = sha1($this->content);
        }

        return $this->hash;
    }

    private ?Output $output = null;

    public function output(): Output
    {
        if (!$this->output) {
            $this->output = $this->transformer->transform($this);
        }

        return $this->output;
    }
}
