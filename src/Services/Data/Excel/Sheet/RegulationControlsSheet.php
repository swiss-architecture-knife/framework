<?php

namespace Swark\Services\Data\Excel\Sheet;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Compliance\Domain\Entity\Control;
use Swark\Services\Data\Excel\Column;
use Swark\Services\Data\Excel\Header;
use Swark\Services\Data\Excel\Import\RowContext;

class RegulationControlsSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle
{
    use Exportable;

    const NAME_COLUMN = 'name';

    const EXTERNAL_ID_COLUMN = 'external_id';
    const CONTENT_COLUMN = 'content';
    const REGULATION_OR_CHAPTER_SCOMP_ID_COLUMN = 'regulation_or_chapter_scomp_id';

    public function generator(): \Generator
    {
        yield ['control-id', 'regulation-scomp-id', 'name', 'content'];
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Control ID', static::EXTERNAL_ID_COLUMN))
            ->add(Column::of('Regulation::scomp_id / Regulation_Chapter::scomp_id', static::REGULATION_OR_CHAPTER_SCOMP_ID_COLUMN))
            ->add(Column::of('Name', static::NAME_COLUMN))
            ->add(Column::of('Content', static::CONTENT_COLUMN))
            ->next()
            ->add(Column::empty())
            ->add(Column::of('${regulation.scomp_id}:${regulation_chapter.external_id} | ${regulation.scomp_id}'))
            ->add(Column::empty(2));
    }

    public function title(): string
    {
        return "Regulation controls";
    }

    protected function importRow(RowContext $row)
    {
        $dataUnique = [
            'external_id' => $row->nonEmpty(static::EXTERNAL_ID_COLUMN),
        ];

        $dataAdditional = [
            'name' => $row->nonEmpty(static::NAME_COLUMN),
            'content' => $row[static::CONTENT_COLUMN],
        ];

        $scomps = $row->explode(":", static::REGULATION_OR_CHAPTER_SCOMP_ID_COLUMN);

        if (sizeof($scomps) >= 1) {
            $dataUnique['regulation_id'] = $this->compositeKeyContainer->get('regulation', $scomps[0]);
        }

        if (sizeof($scomps) >= 2) {
            $dataUnique['regulation_chapter_id'] = $this->compositeKeyContainer->get('regulation_chapter', $scomps[0] . ":" . $scomps[1]);
        }

        $control = Control::updateOrCreate($dataUnique, $dataAdditional);

        $this->compositeKeyContainer->set('regulation_control', $scomps[0] . ":" . $control->external_id, $control->id);
    }
}
