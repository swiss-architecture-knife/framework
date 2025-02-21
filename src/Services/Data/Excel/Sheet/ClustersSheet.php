<?php

namespace Swark\Services\Data\Excel\Sheet;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\HasReferencesToOtherSheets;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Infrastructure\Domain\Entity\Cluster;
use Swark\Services\Data\Excel\Column;
use Swark\Services\Data\Excel\Header;
use Swark\Services\Data\Excel\Import\RowContext;

/**
 * We have to import the clusters sheet in two stages due to possible dependencies.
 * Please note, that this class is tagged with HasReferencesToOtherSheets, so that the Excel sheet is not disconnected and we can load the cluster members in a second pass.
 */
class ClustersSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle, SkipsEmptyRows, HasReferencesToOtherSheets
{
    /** HasR */
    use Exportable;


    const NAME_COLUMN = 'name';

    const MEMBERS_SCOMP_ID_COLUMN = 'members-scomp-id';

    const TYPE_COLUMN = 'type';

    const STAGE_SCOMP_ID_COLUMN = 'stage-scomp-id';

    const NOTES_COLUMN = 'notes';

    public function generator(): \Generator
    {
        yield ['name', 'members-scomp-id', 'type', 'stage', 'notes'];
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::scompId())
            ->add(Column::of('Members', static::MEMBERS_SCOMP_ID_COLUMN))
            ->add(Column::of('Type', static::TYPE_COLUMN))
            ->add(Column::of('Stage', static::STAGE_SCOMP_ID_COLUMN))
            ->add(Column::of('Notes', static::NOTES_COLUMN))
            ->next()
            ->add(Column::empty())
            ->add(Column::of('${${baremetal.scomp_id | runtime.scomp_id | host.scomp_id | application_instance.scomp_id}*}'))
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::empty())
            ;
    }


    public function title(): string
    {
        return "Clusters";
    }

    protected function importRow(RowContext $row)
    {
        $cluster = Cluster::updateOrCreate([
            'name' => $row->nonEmpty(Column::SCOMP_ID_COLUMN),
        ], [
            'stage_id' => $row->ifPresent(static::STAGE_SCOMP_ID_COLUMN, fn($scomp_id) => $this->compositeKeyContainer->get('stage', $scomp_id)),
            'mode' => $row->ifPresent(static::TYPE_COLUMN, fn($type) => $type) ?? null
        ]);

        $this->compositeKeyContainer->set('cluster', $cluster->scomp_id, $cluster->id);
    }
}

