<?php

namespace Swark\Services\Data\Excel\Sheet;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Swark\DataModel\Ecosystem\Domain\Entity\Technology;
use Swark\Services\Data\Excel\Column;
use Swark\Services\Data\Excel\Header;
use Swark\Services\Data\Excel\Import\RowContext;

class TechnologySheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle
{
    use Exportable;

    const NAME_COLUMN = 'name';
    const TYPE_COLUMN = 'type';
    const VERSION_COLUMN = 'version';

    public function generator(): \Generator
    {
        yield ['name', 'scomp-id', 'type', 'version'];
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Name', static::NAME_COLUMN))
            ->add(Column::scompId())
            ->add(Column::of('Type', static::TYPE_COLUMN))
            ->add(Column::of('Version', static::VERSION_COLUMN));
    }

    public function title(): string
    {
        return "Technologies";
    }

    protected function importRow(RowContext $row)
    {
        $technology = Technology::updateOrCreate([
            'name' => $row->nonEmpty(static::NAME_COLUMN),
            'scomp_id' => $row->nonEmpty(Column::SCOMP_ID_COLUMN),
        ], [
            'type' => $row[static::TYPE_COLUMN],
        ]);

        $this->compositeKeyContainer->set('technology', key: $technology->scomp_id, value: $technology->id);
        $latest = $technology->latest();
        $this->compositeKeyContainer->set('technology_version', key: $technology->scomp_id . ":latest", value: $latest->id);

        $versions = $row[static::VERSION_COLUMN];

        if (!empty($versions)) {
            $versions = explode(',', $row[static::VERSION_COLUMN]);

            foreach ($versions as $versionString) {
                $versionString = trim($versionString);
                $version = $technology->versions()->updateOrCreate(['name' => $versionString, 'scomp_id' => $versionString]);
                $this->compositeKeyContainer->set('technology_version', key: $technology->scomp_id . ":" . $versionString, value: $version->id);
            }
        }
    }

    protected function afterSheet(AfterSheet $event)
    {
    }
}

