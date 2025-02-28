<?php

namespace Swark\Services\Data\Excel\Sheet;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\InformationTechnology\Domain\Entity\Component\Runtime;
use Swark\Services\Data\Excel\Column;
use Swark\Services\Data\Excel\Header;
use Swark\Services\Data\Excel\Import\RowContext;

class RuntimesSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle, SkipsEmptyRows
{
    use Exportable;

    const NAME_COLUMN = 'name';
    const HOST_SCOMP_ID_COLUMN = 'host-scomp-id';
    const RELEASE_SCOMP_ID_COLUMN = 'release-scomp-id';

    public function generator(): \Generator
    {
        yield ['name', 'scomp-id', 'host-scomp-id', 'software-release-scomp-id'];
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Name', static::NAME_COLUMN))
            ->add(Column::scompId())
            ->add(Column::of('Host', static::HOST_SCOMP_ID_COLUMN))
            ->add(Column::of('Host', static::RELEASE_SCOMP_ID_COLUMN))
            ->next()
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::of('${host.scomp_id}'))
            ->add(Column::of('${software.scomp_id}:${release.scomp_id}'));
    }


    public function title(): string
    {
        return "Runtimes";
    }

    protected function importRow(RowContext $row)
    {
        $runtime = Runtime::updateOrCreate([
            'name' => $row[static::NAME_COLUMN],
            'scomp_id' => $row->nonEmpty(Column::SCOMP_ID_COLUMN),
            'host_id' => $this->compositeKeyContainer->get('host', $row->nonEmpty(static::HOST_SCOMP_ID_COLUMN)),
            'release_id' => $this->compositeKeyContainer->get('release', $row->nonEmpty(static::RELEASE_SCOMP_ID_COLUMN)),
        ]);

        $this->compositeKeyContainer->set('runtime', $runtime->scomp_id, $runtime->id);
    }
}

