<?php
declare(strict_types=1);

namespace Swark\Content\Domain\Service;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;

class MarkdownService
{
    public function converter($config = []): \League\CommonMark\MarkdownConverter
    {
        $config = [
                'html_input' => 'strip',
                'allow_unsafe_links' => true,
                'renderer' => [
                    'soft_break' => "<br>\r\n",
                ]
            ] + $config;

        $environment = new Environment($config);
        // register GitHub MD and CommonMark
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $converter = new \League\CommonMark\MarkdownConverter($environment);

        return $converter;
    }

    public function convert(?string $content): string
    {
        if (!$content) {
            return '';
        }

        $r = (string)$this->converter()->convert(trim($content) ?? "");
        return trim($r);
    }
}
