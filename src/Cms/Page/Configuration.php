<?php

namespace Swark\Cms\Page;

use Illuminate\Support\Arr;
use Swark\Cms\Chapters\Chapters;

class Configuration
{
    public function __construct(
        public readonly array $config = []
    )
    {
    }

    public static $defaults = [
        'chapters' => [
            'labeling' => [
                'enable_numbering' => false,
                'number' => [
                    'prefix' => null,
                    'concat' => ".",
                    'suffix' => ". ",
                ],
                'label' => [
                    'prefix' => null,
                    'suffix' => null,
                ],
            ],
        ],
    ];

    public function get(string $path): mixed
    {
        return Arr::get($this->config, $path);
    }

    public function hasLabelNumberingEnabled(): bool
    {
        return $this->get('chapters.labeling.enable_numberbing') ?? true;
    }

    public function getNumberPrefix(): string
    {
        return $this->get('chapters.labeling.number.prefix') ?? '';
    }

    public function getNumberSuffix(): string
    {
        return $this->get('chapters.labeling.number.suffix') ?? '';
    }

    public function getNumberConcat(): string
    {
        return $this->get('chapters.labeling.number.concat') ?? '';
    }


    public function getLabelPrefix(): string
    {
        return $this->get('chapters.labeling.label.prefix') ?? '';
    }

    public function getLabelSuffix(): string
    {
        return $this->get('chapters.labeling.label.suffix') ?? '';
    }

    public static function create(array $config = []): Configuration
    {
        $dottedConfig = Arr::dot($config);
        $dottedDefaults = Arr::dot(static::$defaults);
        $config = Arr::undot(array_merge($dottedDefaults, $dottedConfig));

        return new static($config);
    }

    public function mergeFragments(array $fragments = []): array
    {
        if (!isset($this->config['fragments']) || !is_array($this->config['fragments'])) {
            return $fragments;
        }

        $fragmentsFromConfig = Arr::dot($this->config['fragments']);
        $fragments = Arr::dot($fragments);
        $result = array_merge($fragments, $fragmentsFromConfig);
        $fragments = Arr::undot($result);

        return $fragments;
    }

    public function mergeToC(Chapters $toc): Chapters
    {
        if (!isset($this->config['toc']) || !is_array($this->config['toc'])) {
            return $toc;
        }

        // this is a trivial implementation. it does not support removal of any recursive chapters
        $keys = collect($this->config['toc'])->map(fn($key) => array_keys($key)[0])->toArray();

        $newStructure = [];

        foreach ($keys as $key) {
            $fromToc = $toc->find($key);
            if (!$fromToc) {
                continue;
            }

            $newStructure[] = $fromToc->flatten();
        }

        $toc = Chapters::of($newStructure);
        return $toc;
    }
}
