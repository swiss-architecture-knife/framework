<?php

namespace Swark\Services\Data\Excel\Sheet;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Auditing\Domain\Entity\Template;
use Swark\DataModel\Kernel\Infrastructure\Repository\Scope\Scoping;
use Swark\Services\Data\Excel\Column;
use Swark\Services\Data\Excel\Header;
use Swark\Services\Data\Excel\Import\RowContext;

class ScopeTemplatesSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle
{
    use Exportable;

    const NAME_COLUMN = 'name';
    const DESCRIPTION_COLUMN = 'description';
    const INSTANCE_OF_OR_CLASS_COLUMN = 'instance_of_or_class';

    const INSTANCE_PARAMETERS_COLUMN = 'instance_parameters';
    const TEMPLATE_OPTIONS_COLUMN = 'template_options';

    public function generator(): \Generator
    {
        yield ['name', 'scomp-id', 'description', 'Swark\DataModel\Kernel\Infrastructure\Repository\Scope\ItemsByScompId', '{"key":"value"}', ''];
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Name', static::NAME_COLUMN))
            ->add(Column::scompId())
            ->add(Column::of('Description', static::DESCRIPTION_COLUMN))
            ->add(Column::of('Instance of / Class', static::INSTANCE_OF_OR_CLASS_COLUMN))
            ->add(Column::of('Instance parameters', static::INSTANCE_PARAMETERS_COLUMN))
            ->add(Column::of('Template options', static::TEMPLATE_OPTIONS_COLUMN))
            ;
    }

    public function title(): string
    {
        return "Scope templates";
    }

    protected function importRow(RowContext $row)
    {
        $clazz = $row[static::INSTANCE_OF_OR_CLASS_COLUMN];
        throw_if(!class_exists($clazz), "Scope template class '" . $clazz . "' does not exist");

        $instanceParameters = $row->toJson(static::INSTANCE_PARAMETERS_COLUMN, mayNullable: true);

        try {
            $instance = new ($clazz)($instanceParameters);

            throw_if(!($instance instanceof Scoping), "Class " . $clazz . " does not implement interface 'Scoping'");

        } catch (\Exception $e) {
            throw new \Exception("Unable to create defined scope template: " . $e->getMessage());
        }

        $scopeTemplate = Template::updateOrCreate([
            'scomp_id' => $row->nonEmpty(Column::SCOMP_ID_COLUMN),
        ], [
            'name' => $row[static::NAME_COLUMN],
            'description' => $row[static::DESCRIPTION_COLUMN],
            'instance_of' => $clazz,
            'instance_parameters' => $instanceParameters,
            'template_options' => $row->toJson(static::TEMPLATE_OPTIONS_COLUMN, mayNullable: true),
        ]);

        $this->compositeKeyContainer->set('scope_template', $scopeTemplate->scomp_id, $scopeTemplate->id);
    }
}
