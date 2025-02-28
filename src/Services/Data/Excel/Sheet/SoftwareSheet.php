<?php

namespace Swark\Services\Data\Excel\Sheet;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Business\Domain\Entity\Organization;
use Swark\DataModel\Software\Domain\Entity\Software;
use Swark\Services\Data\Excel\Column;
use Swark\Services\Data\Excel\Header;
use Swark\Services\Data\Excel\Import\RowContext;


class SoftwareSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle
{
    use Exportable;

    const NAME_COLUMN = 'name';
    const VENDOR_COLUMN = 'vendor';
    const RELEASES_COLUMN = 'releases';
    const NOTES_COLUMN = 'notes';

    const IS_VIRTUALIZER_COLUMN = 'is_virtualizer';
    const IS_OPERATING_SYSTEM_COLUMN = 'is_operating_system';
    const IS_RUNTIME_COLUMN = 'is_runtime';
    const IS_LIBRARY_COLUMN = 'is_library';


    public function generator(): \Generator
    {
        yield ['name', 'scomp-id', 'vendor', '1.0,2.0', 'x', 'x', 'x', 'x', 'note'];
    }

    public function title(): string
    {
        return "Software";
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Name', static::NAME_COLUMN))
            ->add(Column::scompId())
            ->add(Column::of('Vendor', static::VENDOR_COLUMN))
            ->add(Column::of('Releases', static::RELEASES_COLUMN))
            ->add(Column::of('Is virtualizer', static::IS_VIRTUALIZER_COLUMN))
            ->add(Column::of('Is operating system', static::IS_OPERATING_SYSTEM_COLUMN))
            ->add(Column::of('Is runtime', static::IS_RUNTIME_COLUMN))
            ->add(Column::of('Is library', static::IS_LIBRARY_COLUMN))
            ->add(Column::of('Notes', static::NOTES_COLUMN));
    }

    protected function importRow(RowContext $row)
    {
        $organization = null;
        $vendor = $row[static::VENDOR_COLUMN];

        if (!empty($vendor)) {
            $organization = Organization::updateOrCreate([
                'name' => $vendor,
            ], [
                'is_vendor' => true
            ]);

            $this->compositeKeyContainer->set('organization', $organization->scomp_id, $organization->id);
        }

        $software = Software::updateOrCreate([
            'scomp_id' => $row->nonEmpty(1),
        ], [
            'name' => $row[static::NAME_COLUMN],
            'vendor_id' => $organization ? $organization->id : null,
            'is_virtualizer' => $row->ifPresent(static::IS_VIRTUALIZER_COLUMN, fn() => true) ?? false,
            'is_operating_system' => $row->ifPresent(static::IS_OPERATING_SYSTEM_COLUMN, fn() => true) ?? false,
            'is_runtime' => $row->ifPresent(static::IS_RUNTIME_COLUMN, fn() => true) ?? false,
            'is_library' => $row->ifPresent(static::IS_LIBRARY_COLUMN, fn() => true) ?? false,
        ]);

        $this->compositeKeyContainer->set('software', key: $software->scomp_id, value: $software->id);
        $latestRelease = $software->latest();
        $this->compositeKeyContainer->set('release', key: $software->scomp_id . ":latest", value: $latestRelease->id);

        $releases = $row[static::RELEASES_COLUMN];

        if (!empty($releases)) {
            $versions = explode(',', $releases);

            foreach ($versions as $versionString) {
                $versionString = trim($versionString);
                $version = $software->releases()->updateOrCreate(['version' => $versionString], ['scomp_id' => $versionString]);
                $this->compositeKeyContainer->set('release', key: $software->scomp_id . ":" . $versionString, value: $version->id);
            }
        }
    }
}
