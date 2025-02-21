<?php
declare(strict_types=1);

namespace Swark\Frontend\UI\Components\Diagram;

use Illuminate\View\FileViewFinder;

class Locator
{
    private array $searchPaths = [];

    public function __construct(
        public readonly FileViewFinder $fileViewFinder,
        public readonly Transformer    $transformer,
        public readonly array          $extensions = [],
    )
    {
    }

    public function find(string $id, ?string $fallbackContent = null): Diagram
    {
        $candidates = [];

        try {
            $contentPath = $this->fileViewFinder->find($id . ":" . implode(",", $this->extensions));

            $candidates[] = new Source(
                content: file_get_contents($contentPath),
                transformer: $this->transformer,
                path: $contentPath,
            );
        }
        catch (\Exception $e) {}

        if ($fallbackContent) {
            $candidates[] = new Source(
                content: $fallbackContent,
                transformer: $this->transformer,
                description: 'dynamic'
            );
        }

        return new Diagram(id: $id, sources: $candidates);
    }
}
