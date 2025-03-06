<?php

namespace Swark\Cms\Store;

use Carbon\Carbon;
use Swark\Cms\Content;
use Swark\Cms\Content\OnDemandBody;
use Swark\Cms\Meta\Changes;
use Swark\Cms\ResourceName;
use Swark\Cms\Source;
use Swark\Cms\Store\Search\Search;
use Swark\Cms\Store\Search\ExpressionType;
use Swark\Cms\Store\Search\Expression;
use Swark\Cms\Store\Search\Searchable;
use Swark\Cms\Store\Suggestion\FilesystemSuggestion;
use Swark\Cms\Store\Suggestion\Suggestable;

class FilesystemStore implements Searchable, Suggestable
{
    public function __construct(public readonly string $basePath)
    {
    }

    protected $defaultFilesetExtensions = ['blade\.php', 'md', 'htm[l]'];

    protected function filesetExpression(?string $fileset = null)
    {
        $defaultFileset = 'default';
        $fileset = $fileset ?? $defaultFileset;
        $filesets = [
            $defaultFileset => '\.(' . implode('|', $this->defaultFilesetExtensions) . '?)+',
            'config' => '\.(y[a]?ml)+'
        ];

        return $filesets[$fileset] ?? $filesets[$defaultFileset];
    }

    public function suggest(Search $search): \Generator
    {
        foreach ($search as $searchInPath) {
            if ($searchInPath->queryType !== ExpressionType::EXACT) {
                continue;
            }

            yield new FilesystemSuggestion(
                $searchInPath->resourceName,
                $searchInPath->resourceName->parentToPath() . '/' . $searchInPath->resourceName->child,
                $this->defaultFilesetExtensions);
        }
    }

    private array $pathCache = [];

    /**
     * Creates a new iterator for the given path or none if the path does not exist
     * @param Expression $searchInPath
     * @return \RecursiveIteratorIterator|null
     */
    private function maybeCreateIterator(Expression $searchInPath): \RecursiveIteratorIterator|null
    {
        $subPath = $this->basePath . '/' . $searchInPath->resourceName->parentToPath();

        // we cannot store null as values of hashmap entry, so we have to use false
        $toRecursiveIteratorOrNull = fn($item) => $item === false ? null : $item;

        if (!isset($this->pathCache[$subPath])) {
            $recursiveIteratorIteratorOrNull = match (is_dir($subPath)) {
                true => new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($subPath)),
                false => false
            };

            if ($recursiveIteratorIteratorOrNull) {
                yo_debug('Content available in path: %s (%s)', [$subPath, $searchInPath->resourceName]);
            } else {
                yo_debug('Content directory "%s" does not exist', [$subPath]);
            }

            $this->pathCache[$subPath] = $recursiveIteratorIteratorOrNull;
        }

        return $toRecursiveIteratorOrNull($this->pathCache[$subPath]);
    }

    public function search(Search $search): \Generator
    {
        /** @var Expression $searchInPath */
        foreach ($search as $searchInPath) {
            $iterator = $this->maybeCreateIterator($searchInPath);

            if (!$iterator) {
                continue;
            }

            $fileRegex = $this->filesetExpression($searchInPath->resourceName->fileset);

            switch ($searchInPath->queryType) {
                case ExpressionType::IN:
                    $regPattern = '/.*\/([a-zA-Z0-9\-]+)' . $fileRegex . '$/';
                    break;
                case ExpressionType::EXACT:
                    $regPattern = '/.*(' . preg_quote($searchInPath->resourceName->child, '/') . ')+' . $fileRegex . '$/';
                    break;
            }

            yo_info($regPattern);

            $files = new \RegexIterator($iterator, $regPattern, \RegexIterator::GET_MATCH);

            foreach ($files as $file) {
                yo_info('File found for URL %s at %s ', [$file[1], $file[0]]);

                yield $this->emit($file[0], $file[1], $file[2], $searchInPath->resourceName->fileset);
            }
        }
    }

    private function emit(string $absolutePath, string $scompId, string $fileType, ?string $fileset = null): Content
    {
        $path = str_replace($this->basePath, '', $absolutePath);
        // strip file extension
        $path = str_replace('.' . $fileType, '', $path);

        if ($fileset) {
            $path .= ':' . $fileset;
        }

        $resourceName = ResourceName::of($path);

        yo_info("Full resource name: " . $resourceName->full);
        $r = new Content(
            resourceName: $resourceName,
            createdAt: Carbon::now(),
            body: OnDemandBody::ofFile($absolutePath, $fileType),
            source: Source::of('fs', $absolutePath),
            changes: Changes::anonymous(Carbon::now()),
        );

        return $r;
    }
}
