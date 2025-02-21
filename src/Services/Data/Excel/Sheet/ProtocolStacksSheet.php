<?php

namespace Swark\Services\Data\Excel\Sheet;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Ecosystem\Domain\Entity\ProtocolStack;
use Swark\Services\Data\Excel\Column;
use Swark\Services\Data\Excel\Header;
use Swark\Services\Data\Excel\Import\RowContext;


class ProtocolStacksSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle
{
    use Exportable;

    const NAME_COLUMN = 'name';
    const PORT_COLUMN = 'port';
    const APPLICATION_LAYER_COLUMN = 'layer_7';
    const PRESENTATION_LAYER_COLUMN = 'layer_6';
    const SESSION_LAYER_COLUMN = 'layer_5';
    const TRANSPORT_LAYER_COLUMN = 'layer_4';
    const NETWORK_LAYER_COLUMN = 'layer_3';

    public function generator(): \Generator
    {
        yield ['name', '8080', 'layer-7', 'layer-6', 'layer-5', 'layer-4', 'layer-3'];
    }

    public function title(): string
    {
        return "Protocol stacks";
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Name', static::NAME_COLUMN))
            ->add(Column::of('Port', static::PORT_COLUMN))
            ->add(Column::of('Application layer', static::APPLICATION_LAYER_COLUMN))
            ->add(Column::of('Presentation layer', static::PRESENTATION_LAYER_COLUMN))
            ->add(Column::of('Session layer', static::SESSION_LAYER_COLUMN))
            ->add(Column::of('Transport layer', static::TRANSPORT_LAYER_COLUMN))
            ->add(Column::of('Network layer', static::NETWORK_LAYER_COLUMN))
            ->next()
            ->add(Column::empty(2))
            ->add(Column::of('${technology[type=data-format].scomp_id}'))
            ->add(Column::of('${technology[type=protocol].scomp_id}'))
            ->add(Column::of('${technology[type=protocol].scomp_id}'))
            ->add(Column::of('${technology[type=protocol].scomp_id}'))
            ->add(Column::of('${technology[type=protocol].scomp_id}'));
    }

    protected function importRow(RowContext $row)
    {
        $port = $row[static::PORT_COLUMN];
        $protocolStack = ProtocolStack::updateOrCreate([
            'name' => $row->nonEmpty(static::NAME_COLUMN),
        ], [
            'port' => is_int($port) ? $port : null,
            'application_layer_id' => $this->compositeKeyContainer->get('technology_version', $row[static::APPLICATION_LAYER_COLUMN] . ':latest'),
            'presentation_layer_id' => $this->compositeKeyContainer->get('technology_version', $row[static::PRESENTATION_LAYER_COLUMN] . ':latest'),
            'session_layer_id' => $this->compositeKeyContainer->get('technology_version', $row[static::SESSION_LAYER_COLUMN] . ':latest'),
            'transport_layer_id' => $this->compositeKeyContainer->get('technology_version', $row[static::TRANSPORT_LAYER_COLUMN] . ':latest'),
            'network_layer_id' => $this->compositeKeyContainer->get('technology_version', $row[static::NETWORK_LAYER_COLUMN] . ':latest'),
        ]);

        $this->compositeKeyContainer->set('protocol_stack', key: Str::lower($protocolStack->name), value: $protocolStack->id);
    }
}
