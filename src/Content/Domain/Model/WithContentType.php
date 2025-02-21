<?php

namespace Swark\Content\Domain\Model;

interface WithContentType
{
    public function contentType(): ContentType;
}
