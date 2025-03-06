<?php

namespace Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\SoftwareArchitecture;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Infrastructure\Eloquent\Model\Business\Organization;
use Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture\Software;
use Swark\DataModel\Infrastructure\Exchange\Excel\Column;
use Swark\DataModel\Infrastructure\Exchange\Excel\Header;
use Swark\DataModel\Infrastructure\Exchange\Excel\RowContext;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\AbstractSwarkExcelSheet;


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
            $organization = Organization::upsert([
                'name' => $vendor,
            ], [
                'is_vendor' => true
            ]);

            $this->compositeKeyContainer->set('organization', $organization->configurationItem->scomp_id, $organization->id);
        }

        $software = Software::upsert(
            $row->nonEmpty(1),
            [
                'name' => $row[static::NAME_COLUMN],
                'vendor_id' => $organization ? $organization->id : null,
                'is_virtualizer' => $row->ifPresent(static::IS_VIRTUALIZER_COLUMN, fn() => true) ?? false,
                'is_operating_system' => $row->ifPresent(static::IS_OPERATING_SYSTEM_COLUMN, fn() => true) ?? false,
                'is_runtime' => $row->ifPresent(static::IS_RUNTIME_COLUMN, fn() => true) ?? false,
                'is_library' => $row->ifPresent(static::IS_LIBRARY_COLUMN, fn() => true) ?? false,
            ]);

        $this->compositeKeyContainer->set('software', key: $software->configurationItem->scomp_id, value: $software->id);
        $latestRelease = $software->latest();
        $this->compositeKeyContainer->set('release', key: $software->configurationItem->scomp_id . ":latest", value: $latestRelease->id);

        $releases = $row[static::RELEASES_COLUMN];

        if (!empty($releases)) {
            $versions = explode(',', $releases);

            foreach ($versions as $versionString) {
                $versionString = trim($versionString);
                $version =
                    $software
                        ->releases()
                        ->updateOrCreate(['version' => $versionString], ['is_latest' => true]);
                $this->compositeKeyContainer->set('release', key: $software->configurationItem->scomp_id . ":" . $versionString, value: $version->id);
            }
        }
    }
}
