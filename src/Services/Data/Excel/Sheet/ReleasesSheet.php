<?php

namespace Swark\Services\Data\Excel\Sheet;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\SoftwareArchitecture\Domain\Entity\Release;
use Swark\Services\Data\Excel\Column;
use Swark\Services\Data\Excel\Header;
use Swark\Services\Data\Excel\Import\RowContext;

class ReleasesSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle, SkipsEmptyRows
{
    use Exportable;

    const SOFTWARE_SCOMP_ID_COLUMN = 'software-scomp-id';
    const VERSION_COLUMN = 'version';

    public function generator(): \Generator
    {
        yield ['software-scomp-id', 'version'];
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Software::scomp_id', static::SOFTWARE_SCOMP_ID_COLUMN))
            ->add(Column::of('Release', static::VERSION_COLUMN))
            ->next()
            ->add(Column::of('${software.scomp_id}'))
            ->add(Column::empty())
            ;
    }


    public function title(): string
    {
        return "Releases";
    }

    protected function importRow(RowContext $row)
    {
        $release = Release::updateOrCreate([
            'software_id' => $this->compositeKeyContainer->get('software', $row->nonEmpty(static::SOFTWARE_SCOMP_ID_COLUMN)),
            'version' => $row->nonEmpty(static::VERSION_COLUMN)
        ]);

        $this->compositeKeyContainer->set('release', $row[static::SOFTWARE_SCOMP_ID_COLUMN] . ":" . $row[static::VERSION_COLUMN], $release->id);
    }
}

