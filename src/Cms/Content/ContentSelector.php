<?php

namespace Swark\Cms\Content;

use Swark\Content\Domain\Model\ContentType;

class ContentSelector
{
    public function select(array $blocks): ?\Swark\Cms\Content
    {
        if (sizeof($blocks) == 0) {
            return new NotFound();
        }

        $r = null;

        /** @var \Swark\Cms\Content $block */
        foreach ($blocks as $block) {
            // favor database over everything else
            if ($block->source === 'database') {
                $r = $block;;
                break;
            }

            // favor markdown over everything else
            if ($block->body->contentType() == ContentType::BLADE) {
                $r = $block;
                break;
            }

            $r = $block;
        }

        return $r;
    }
}
