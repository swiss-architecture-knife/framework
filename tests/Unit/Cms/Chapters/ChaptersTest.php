<?php

namespace Swark\Tests\Unit\Cms\Chapters;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Swark\Cms\Chapters\Chapters;
use Swark\Cms\Chapters\NoMoreChaptersException;

class ChaptersTest extends TestCase
{
    #[Test]
    public function canNotPull_moreThanExisting()
    {
        $chapters = [
            ['a', 'A'],
        ];

        $sut = new Chapters($chapters);
        $first = $sut->pull();
        $this->assertEquals('a', $first->id);

        $this->expectException(NoMoreChaptersException::class);
        $r = $sut->pull();
    }

    #[Test]
    public function canPull_moreThanOneChapter(): void
    {
        $chapters = [
            ['a', 'A'],
            ['b', 'B'],];

        $sut = new Chapters($chapters);

        $first = $sut->pull();
        $this->assertEquals('a', $first->id);

        $second = $sut->pull();
        $this->assertEquals('b', $second->id);
    }

    #[Test]
    public function canPull_nestedOneChapter(): void
    {
        $chapters = [
            ['a', 'A', [
                ['a-1', 'A1']
            ]],
            ['b', 'B'],];

        $sut = new Chapters($chapters);

        $first = $sut->pull();
        $this->assertEquals('a', $first->id);

        $second = $sut->pull();
        $this->assertEquals('a-1', $second->id);

        $third = $sut->pull();
        $this->assertEquals('b', $third->id);
    }

    #[Test]
    public function canPull_multipleNestedChapters(): void
    {
        $chapters = [
            [
                'a', 'A', [
                ['a-1', 'A1']
            ]
            ],
            ['b', 'B', [
                ['b-1', 'B1'],
                ['b-2', 'B2'],
            ]
            ]
        ];

        $sut = new Chapters($chapters);

        $first = $sut->pull();
        $this->assertEquals('a', $first->id);

        $second = $sut->pull();
        $this->assertEquals('a-1', $second->id);

        $third = $sut->pull();
        $this->assertEquals('b', $third->id);

        $fourth = $sut->pull();
        $this->assertEquals('b-1', $fourth->id);

        $fifth = $sut->pull();
        $this->assertEquals('b-2', $fifth->id);
    }
}

