<?php

namespace Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Governance\Kpi;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Kpi\Period;
use Swark\DataModel\Infrastructure\Exchange\Excel\Column;
use Swark\DataModel\Infrastructure\Exchange\Excel\Header;
use Swark\DataModel\Infrastructure\Exchange\Excel\RowContext;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\AbstractSwarkExcelSheet;

class MeasurementPeriodsSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle
{
    use Exportable;

    const NAME_COLUMN = 'name';
    const DESCRIPTION_COLUMN = 'description';
    const BEGIN_AT_COLUMN = 'begin_at';
    const END_AT_COLUMN = 'end_at_or_duration';

    public function generator(): \Generator
    {
        yield ['name', 'scomp-id', 'description', 'begin at', 'end at'];
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Name', static::NAME_COLUMN))
            ->add(Column::scompId())
            ->add(Column::of('Description', static::DESCRIPTION_COLUMN))
            ->add(Column::of('Begin at', static::BEGIN_AT_COLUMN))
            ->add(Column::of('End at', static::END_AT_COLUMN))
            ->
            next()
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::empty());
    }

    public function title(): string
    {
        return "Measurement periods";
    }

    private ?Period $default = null;

    protected function importRow(RowContext $row)
    {
        $measurementPeriod = Period::upsert(
            $row->nonEmpty(Column::SCOMP_ID_COLUMN),
            [
                'name' => $row[static::NAME_COLUMN],
                'description' => $row[static::DESCRIPTION_COLUMN],
                'begin_at' => $row[static::BEGIN_AT_COLUMN],
                'end_at' => $row[static::END_AT_COLUMN],
            ]
        );

        $this->compositeKeyContainer->set('measurement_period', $measurementPeriod->configurationItem->scomp_id, $measurementPeriod->id);

        if (!$this->default) {
            $this->compositeKeyContainer->set('measurement_period', 'default', $measurementPeriod->id);
            $this->default = $measurementPeriod;
        }
    }
}

