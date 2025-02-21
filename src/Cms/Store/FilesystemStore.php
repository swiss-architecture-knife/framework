<?php

namespace Swark\Cms\Store;

use Carbon\Carbon;
use Illuminate\Support\HtmlString;
use Swark\Cms\Content;
use Swark\Cms\Content\OnDemandBody;
use Swark\Cms\Meta\Changes;
use Swark\Cms\Meta\Path;
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

        return $filesets[$fileset] ?? $fileset[$defaultFileset];
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

    public function search(Search $search): \Generator
    {
        /** @var Expression $searchInPath */
        foreach ($search as $searchInPath) {
            $subPath = $this->basePath . '/' . $searchInPath->resourceName->parentToPath();
            yo_info('Searching for content in path: %s (%s)', [$subPath, $searchInPath->resourceName]);
            $rec = null;

            try {
                $rec = new \RecursiveDirectoryIterator($subPath);
            } catch (\Exception $e) {
                yo_error($e->getMessage());
                continue;
            }

            $ite = new \RecursiveIteratorIterator($rec);
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

            $files = new \RegexIterator($ite, $regPattern, \RegexIterator::GET_MATCH);

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
