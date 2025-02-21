<?php

namespace Swark\Tests\Feature\Content;

use PHPUnit\Framework\Attributes\Test;
use Swark\Cms\ResourceName;
use Swark\Tests\IntegrationTestCase;

class ResourceNameTest extends IntegrationTestCase
{
    #[Test]
    public function whenNoCurrentPath_thenReturnRequested(): void
    {
        $this->assertEquals('s-ad', (string)ResourceName::of('s-ad'));
    }

    #[Test]
    public function whenCurrentPath_thenResolveToPath(): void
    {
        $this->get("/s-ad/chapter-1");
        $this->assertEquals('s-ad__chapter-1', ResourceName::of('~'));
    }

    #[Test]
    public function whenCurrentPathWithSubResource_thenResolveToPath(): void
    {
        $this->get("/s-ad/chapter-1");
        $this->assertEquals('s-ad__chapter-1__subresource', ResourceName::of('~/subresource'));
        $this->assertEquals('s-ad__chapter-1__subresource', ResourceName::of('~subresource'));
    }

    #[Test]
    public function whenOnlyRelativePathIsProvided_thenResolveToPath(): void {
        $this->assertEquals('s-ad__chapter-1', ResourceName::of('/s-ad/chapter-1'));
    }

    #[Test]
    public function whenFilesetGiven_itIsResolved(): void {
        $sut = ResourceName::of('s-ad/chapter-1:config');
        $this->assertEquals('s-ad__chapter-1', (string)$sut);
        $this->assertEquals('config', $sut->fileset);
    }
}
