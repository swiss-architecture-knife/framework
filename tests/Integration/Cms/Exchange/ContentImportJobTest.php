<?php

namespace Swark\Tests\Integration\Cms\Exchange;

use Carbon\Carbon;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Swark\Cms\Application\Exchange\ContentImportJob;
use Swark\Cms\Application\Exchange\ContentImportOptions;
use Swark\Cms\Domain\Model\Exchange\ContentImportResult;
use Swark\Cms\Domain\Model\Exchange\ContentImportStatus;
use Swark\Cms\Infrastructure\Eloquent\Model\Content;
use Swark\Cms\Infrastructure\Exchange\Database\ImportableDatabaseContent;
use Swark\Tests\IntegrationTestCase;

class ContentImportJobTest extends IntegrationTestCase
{
    #[Test]
    public function importableFiles_canBeFound_andImported(): void
    {
        // given

        $sut = new ContentImportJob(new ContentImportOptions(__DIR__ . '/testdata/'));

        $this->mock(ImportableDatabaseContent::class, function (MockInterface $mock) {
            $mock->shouldReceive('import')->once()->andReturn(new Content(['scomp_id' => 555]));
            $mock->shouldReceive('findKnownContent')->once()->andReturn([]);
        });

        // when
        /** @var ContentImportResult[] $result */
        $result = $sut->run();

        // then
        $this->assertEquals(1, sizeof($result));
        $this->assertEquals(ContentImportStatus::SUCCESS, $result[0]->status);
        $this->assertEquals('strategy/importable.md', $result[0]->localPath);
    }

    #[Test]
    public function olderFiles_areIngored(): void
    {
        // given

        $sut = new ContentImportJob(new ContentImportOptions(__DIR__ . '/testdata-ignored/'));

        $this->mock(ImportableDatabaseContent::class, function (MockInterface $mock) {
            $mock->shouldReceive('findKnownContent')->once()->andReturn(['strategy_index' => Carbon::today()]);
            $mock->shouldReceive('import')->never();
        });

        // when
        /** @var ContentImportResult[] $result */
        $result = $sut->run();

        // then
        $this->assertEquals(1, sizeof($result));
        $this->assertEquals(ContentImportStatus::WARNING, $result[0]->status);
        $this->assertEquals('strategy/index.md', $result[0]->localPath);
        $this->assertMatchesRegularExpression('/it is already updated/', $result[0]->message);
    }

    #[Test]
    public function unknownFiles_areWarned(): void
    {
        // given

        $sut = new ContentImportJob(new ContentImportOptions(__DIR__ . '/testdata-invalid/'));

        $this->mock(ImportableDatabaseContent::class, function (MockInterface $mock) {
            $mock->shouldReceive('findKnownContent')->once()->andReturn([]);
            $mock->shouldReceive('import')->never();
        });

        // when
        /** @var ContentImportResult[] $result */
        $result = $sut->run();

        // then
        $this->assertEquals(1, sizeof($result));
        $this->assertEquals(ContentImportStatus::WARNING, $result[0]->status);
        $this->assertEquals('strategy/invalid.blade.php', $result[0]->localPath);
        $this->assertMatchesRegularExpression('/Unknown file extension/', $result[0]->message);
    }
}
