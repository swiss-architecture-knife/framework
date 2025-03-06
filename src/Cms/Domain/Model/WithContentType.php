<?php

namespace Swark\Cms\Domain\Model;

interface WithContentType
{
    public function contentType(): ContentType;
}
