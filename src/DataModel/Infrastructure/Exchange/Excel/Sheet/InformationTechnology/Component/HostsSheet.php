<?php

namespace Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\InformationTechnology\Component;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\HasReferencesToOtherSheets;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Host;
use Swark\DataModel\Infrastructure\Exchange\Excel\Column;
use Swark\DataModel\Infrastructure\Exchange\Excel\Header;
use Swark\DataModel\Infrastructure\Exchange\Excel\RowContext;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\AbstractSwarkExcelSheet;

class  HostsSheet extends AbstractSwarkExcelSheet implements HasReferencesToOtherSheets, FromGenerator, WithTitle, SkipsEmptyRows
{
    use Exportable;

    const NAME_COLUMN = 'name';
    const PARENT_SCOMP_ID_COLUMN = 'parent-scomp-id';
    const OPERATING_SYSTEM_SCOMP_ID_COLUMN = 'operating-system-scomp-id';

    const VIRTUALIZER_SCOMP_ID_COLUMN = 'virtualizer-scomp-id';
    const IP_ADDRESSES_COLUMN = 'ip_addresses';
    const DNS_NAMES_COLUMN = 'dns_names';
    const NOTES_COLUMN = 'notes';

    public function generator(): \Generator
    {
        yield ['name', 'scomp-id', 'parent-scomp-id', 'operating-system-scomp-id', 'virtualizer-scomp-id', 'ip-addresses', 'dns-names', 'notes'];
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Name', static::NAME_COLUMN))
            ->add(Column::scompId())
            ->add(Column::of('Parent', static::PARENT_SCOMP_ID_COLUMN))
            ->add(Column::of('Operating system', static::OPERATING_SYSTEM_SCOMP_ID_COLUMN))
            ->add(Column::of('Virtualizer', static::VIRTUALIZER_SCOMP_ID_COLUMN))
            ->add(Column::of('IP addresses', static::IP_ADDRESSES_COLUMN))
            ->add(Column::of('DNS names', static::DNS_NAMES_COLUMN))
            ->add(Column::of('Notes', static::NOTES_COLUMN))
            ->next()
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::of('${baremetal.scomp_id | host.scomp_id}'))
            ->add(Column::of('${software.scomp_id}:${release.scomp_id}'))
            ->add(Column::of('?($software.scomp_id}:${release.scomp_id})'))
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

        $allowedTypes = ['baremetal', 'host', 'cluster'];

        throw_if(!in_array($parent[0], $allowedTypes), "Unknown type '" . $parent[0] . "'. Only a type of [" . implode(", ", $allowedTypes) . "] is allowed (Excel row: $row->rowNumber})");

        if ($parent[0] == 'baremetal') {
            $baremetalId = $this->compositeKeyContainer->get('baremetal', $parent[1]);
        } elseif ($parent[0] == 'host') {
            $parentHostId = $this->compositeKeyContainer->get('host', $parent[1]);
        }
        elseif ($parent[0] == 'cluster') {
            // handled in HostsBelongingToClusterSheet
            // this is fine: no error, we still have to set up the host
        }

        $host = Host::upsert(
            $row->nonEmpty(Column::SCOMP_ID_COLUMN),
            [
                'name' => $row[static::NAME_COLUMN],
                'operating_system_id' => $this->compositeKeyContainer->get('release', $row->nonEmpty(static::OPERATING_SYSTEM_SCOMP_ID_COLUMN)),
                'virtualizer_id' => $row->ifPresent(static::VIRTUALIZER_SCOMP_ID_COLUMN, fn($item) => $this->compositeKeyContainer->get('release', $row->nonEmpty(static::VIRTUALIZER_SCOMP_ID_COLUMN))),
                'parent_host_id' => $parentHostId,
                'baremetal_id' => $baremetalId,
            ]);

        $this->compositeKeyContainer->set('host', $host->configurationItem->scomp_id, $host->id);
    }
}

