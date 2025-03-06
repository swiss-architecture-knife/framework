<?php

namespace Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Compliance;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\Chapter;
use Swark\DataModel\Infrastructure\Exchange\Excel\Column;
use Swark\DataModel\Infrastructure\Exchange\Excel\Header;
use Swark\DataModel\Infrastructure\Exchange\Excel\RowContext;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\AbstractSwarkExcelSheet;

class RegulationChaptersSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle
{
    use Exportable;

    const NAME_COLUMN = 'name';

    const EXTERNAL_ID_COLUMN = 'external_id';
    const OFFICIAL_CONTENT_COLUMN = 'official_content';
    const ACTUAL_STATUS_COLUMN = 'actual_status';
    const TARGET_STATUS_COLUMN = 'target_status';
    const REGULATION_SCOMP_ID_COLUMN = 'regulation_scomp_id';

    public function generator(): \Generator
    {
        yield ['name', 'scomp-id'];
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Chapter number', static::EXTERNAL_ID_COLUMN))
            ->add(Column::of('Regulation::scomp_id', static::REGULATION_SCOMP_ID_COLUMN))
            ->add(Column::of('Name', static::NAME_COLUMN))
            ->add(Column::of('Official content', static::OFFICIAL_CONTENT_COLUMN))
            ->add(Column::of('Actual status', static::ACTUAL_STATUS_COLUMN))
            ->add(Column::of('Target status', static::TARGET_STATUS_COLUMN))
            ->next()
            ->add(Column::of('regulation_chapter.external_id'))
            ->add(Column::empty(4));
    }

    public function title(): string
    {
        return "Regulation chapters";
    }

    protected function importRow(RowContext $row)
    {
        $regulationScompId = $row->nonEmpty(self::REGULATION_SCOMP_ID_COLUMN);

        $chapter = Chapter::updateOrCreate([
            'external_id' => $row->nonEmpty(static::EXTERNAL_ID_COLUMN),
        ], [
            'name' => $row[static::NAME_COLUMN],
            'official_content' => $row[static::OFFICIAL_CONTENT_COLUMN],
            'actual_status' => $row[static::ACTUAL_STATUS_COLUMN],
            'target_status' => $row[static::TARGET_STATUS_COLUMN],
            'regulation_id' => $this->compositeKeyContainer->get('regulation', $regulationScompId)
        ]);

        $this->compositeKeyContainer->set('regulation_chapter', $regulationScompId . ":" . $chapter->external_id, $chapter->id);
    }
}

// TODO: Before Import -> Filesystem
