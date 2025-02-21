<?php

namespace Swark\Content\Domain\Factory;

use Swark\Content\Domain\Model\Markdown;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class MarkdownFactory
{
    public static function create(string $content): Markdown
    {
        $content = trim($content);
        $position = 0;

        $yaml = null;

        if (preg_match_all('/^---([\s\S]*?)---/ui', $content, $matches)) {
            $frontMatter = trim($matches[1][0]);
            $position = strlen($matches[0][0]);
            $content = trim(substr($content, $position));

            $yaml = Yaml::parse($frontMatter);
        }

        return new Markdown($content, $position, $yaml);
    }

    public static function read(string $path): ?Markdown
    {
        if (!file_exists($path)) {
            return null;
        }

        try {
            return static::create(file_get_contents($path));
        } catch (ParseException $e) {
            throw new \Exception("Unable to parse " . $path . ": " . $e->getMessage());
        }
    }
}
