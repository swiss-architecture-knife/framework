<?php

namespace Swark\Tests\Integration\DataModel\Exchange;

use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\Chapter;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\Control;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\Regulation;
use Swark\DataModel\Infrastructure\Exchange\Database\ImportableDatabaseRegulation;
use Swark\DataModel\Infrastructure\Exchange\Filesystem\DataModelFilesystemImporter;
use Swark\Tests\IntegrationTestCase;

class DataModelFilesystemImporterTest extends IntegrationTestCase
{
    #[Test]
    public function regulationsFromFilesystem_canDetectRegulations(): void
    {
        // when
        $sut = new DataModelFilesystemImporter(
            __DIR__ . '/testdata-datamodel-markdown-yaml',
            enableRegulations: true);

        $importables = $sut->importables();

        $this->assertEquals(1, count($importables));
        $this->assertEquals('nis2', $importables[0]->regulation);
    }

    #[Test]
    public function regulationsFromFilesystem_canBeImported(): void
    {
        // given
        $this->mock(ImportableDatabaseRegulation::class, function (MockInterface $mock) {
            $mock->shouldReceive('upsertRegulation')
                ->once()
                ->withArgs(
                    fn($args) => (
                        $args[0] == 'nis2'
                        && $args[1]['name'] == 'nis2'
                    )
                )
                ->andReturn(new Regulation(['id' => 555]));

            $mock->shouldReceive('upsertChapter')
                ->once()
                ->withArgs(
                    fn($args) => (
                        $args[0]['external_id'] == '24'
                        && $args[1]['name'] == 'Nutzung der europäischen Schemata für die Cybersicherheitszertifizierung'
                    )
                )
                ->andReturnUsing(function () {
                    $r = new Chapter(['external_id' => 24]);
                    // ID is not fillable
                    $r->id = 666;
                    return $r;
                });


            $mock->shouldReceive('upsertControl')
                ->once()
                ->withArgs(
                    fn($args) => (
                        $args[0]['external_id'] == '24.1'
                        && $args[1]['name'] == 'Meldepflicht von Sicherheitsvorfällen an Behörden'
                    )
                )
                ->andReturnUsing(function () {
                    $r = new Control(['external_id' => '24.1']);
                    // ID is not fillable
                    $r->id = 777;
                    return $r;
                });
        });

        // when
        $sut = new DataModelFilesystemImporter(
            __DIR__ . '/testdata-datamodel-markdown-yaml',
            enableRegulations: true);

        $sut->import();

    }
}
