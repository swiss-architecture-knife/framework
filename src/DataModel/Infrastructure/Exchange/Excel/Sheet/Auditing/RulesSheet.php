<?php

namespace Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Auditing;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Infrastructure\Eloquent\Model\Auditing\Rule;
use Swark\DataModel\Infrastructure\Eloquent\Model\Auditing\Scope;
use Swark\DataModel\Infrastructure\Exchange\Excel\Column;
use Swark\DataModel\Infrastructure\Exchange\Excel\Header;
use Swark\DataModel\Infrastructure\Exchange\Excel\RowContext;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\AbstractSwarkExcelSheet;

class RulesSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle
{
    use Exportable;

    const NAME_COLUMN = 'name';
    const POLICY_SCOMP_ID = 'policy_scomp_id';
    const DESCRIPTION_COLUMN = 'description';
    const REFERENCED_SCOPES_COLUMN = 'referenced_scopes';

    public function generator(): \Generator
    {
        yield ['name', 'scomp-id', 'description'];
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Name', static::NAME_COLUMN))
            ->add(Column::of('Policy::scomp_id', static::POLICY_SCOMP_ID))
            ->add(Column::scompId())
            ->add(Column::of('Description', static::DESCRIPTION_COLUMN))
            ->add(Column::of('Referenced scopes', static::REFERENCED_SCOPES_COLUMN))
            ->next()
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::of('${scope_template.scomp_id:$JSON} [EOL,…]'))
            ;
    }

    public function title(): string
    {
        return "Rules";
    }

    protected function importRow(RowContext $row)
    {
        $rule = Rule::upsert($row->nonEmpty(Column::SCOMP_ID_COLUMN),
            [
            'policy_id' => $this->compositeKeyContainer->get('policy', $row->nonEmpty(static::POLICY_SCOMP_ID)),
            'name' => $row[static::NAME_COLUMN],
            'description' => $row[static::DESCRIPTION_COLUMN],
        ]);

        $this->compositeKeyContainer->set('rule', $rule->configurationItem->scomp_id, $rule->id);

        $referencedScopeTemplates = $row[static::REFERENCED_SCOPES_COLUMN];
        $referencedScopeTemplates = explode(PHP_EOL, $referencedScopeTemplates);

        foreach ($referencedScopeTemplates as $referencedScopeTemplate) {
            $scompId = substr($referencedScopeTemplate, 0, strpos($referencedScopeTemplate, ":"));
            $scopeTemplateId = $this->compositeKeyContainer->get('scope_template', $scompId);
            $options = substr($referencedScopeTemplate, strpos($referencedScopeTemplate, ":") + 1);
            $options = RowContext::convertFromJsonStringToObject($options, mayNullable: true);

            Scope::updateOrCreate(['scope_template_id' => $scopeTemplateId, 'rule_id' => $rule->id], ['options' => $options]);
        }
    }
}
