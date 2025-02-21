<?php

namespace Swark\Services\Data\Excel\Sheet;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Infrastructure\Domain\Entity\Host;
use Swark\Services\Data\Excel\Column;
use Swark\Services\Data\Excel\Header;
use Swark\Services\Data\Excel\Import\RowContext;

class HostsSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle, SkipsEmptyRows
{
    use Exportable;

    const NAME_COLUMN = 'name';
    const PARENT_SCOMP_ID_COLUMN = 'parent-scomp-id';
    const OPERATING_SYSTEM_SCOMP_ID_COLUMN = 'operating-system-scomp-id';
    const IP_ADDRESSES_COLUMN = 'ip_addresses';
    const DNS_NAMES_COLUMN = 'dns_names';
    const NOTES_COLUMN = 'notes';

    public function generator(): \Generator
    {
        yield ['name', 'scomp-id', 'parent-scomp-id', 'operating-system-scomp-id', 'ip-addresses', 'dns-names', 'notes'];
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Name', static::NAME_COLUMN))
            ->add(Column::scompId())
            ->add(Column::of('Parent', static::PARENT_SCOMP_ID_COLUMN))
            ->add(Column::of('Operating system', static::OPERATING_SYSTEM_SCOMP_ID_COLUMN))
            ->add(Column::of('IP addresses', static::IP_ADDRESSES_COLUMN))
            ->add(Column::of('DNS names', static::DNS_NAMES_COLUMN))
            ->add(Column::of('Notes', static::NOTES_COLUMN))
            ->next()
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::of('${cluster.scomp_id | baremetal.scomp_id}'))
            ->add(Column::of('${software.scomp_id}:${release.scomp_id}'))
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::empty());
    }


    public function title(): string
    {
        return "Hosts";
    }

    protected function importRow(RowContext $row)
    {
        $baremetalId = null;
        $parentHostId = null;

        $parent = $row->explode(":", static::PARENT_SCOMP_ID_COLUMN);

        if ($parent[0] == 'baremetal') {
            $baremetalId = $this->compositeKeyContainer->get('baremetal', $parent[1]);
        } elseif ($parent[0] == 'host') {
            $parentHostId = $this->compositeKeyContainer->get('host', $parent[1]);
        } elseif ($parent[0] == 'cluster') {
            // ignore; this is handled by "Clusters" sheet
        } else {
            throw new \Exception("Parent type {$parent[0]} not implemented yet");
        }

        $host = Host::updateOrCreate([
            'name' => $row[static::NAME_COLUMN],
            'scomp_id' => $row->nonEmpty(Column::SCOMP_ID_COLUMN),
            'operating_system_id' => $this->compositeKeyContainer->get('release', $row->nonEmpty(static::OPERATING_SYSTEM_SCOMP_ID_COLUMN)),
            'parent_host_id' => $parentHostId,
            'baremetal_id' => $baremetalId,
        ]);

        $this->compositeKeyContainer->set('host', $host->scomp_id, $host->id);
    }
}

