<?php

namespace Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Auditing;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Infrastructure\Eloquent\Model\Auditing\Finding;
use Swark\DataModel\Infrastructure\Exchange\Excel\Column;
use Swark\DataModel\Infrastructure\Exchange\Excel\Header;
use Swark\DataModel\Infrastructure\Exchange\Excel\RowContext;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\AbstractSwarkExcelSheet;

class FindingsSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle
{
    use Exportable;

    const NAME_COLUMN = 'name';
    const DESCRIPTION_COLUMN = 'description';
    const CRITICALITY_SCOMP_ID_COLUMN = 'criticality_id';
    const CONTROL_SCOMP_ID_COLUMN = 'control_scomp_id';
    const REFERENCES_COLUMN = 'references';

    public function generator(): \Generator
    {
        yield ['name', 'scomp-id', 'description', 'control-scomp-id', 'references', 'criticality-id'];
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Name', static::NAME_COLUMN))
            ->add(Column::scompId())
            ->add(Column::of('Description', static::DESCRIPTION_COLUMN))
            ->add(Column::of('Control::scomp_id', static::CONTROL_SCOMP_ID_COLUMN))
            ->add(Column::of('References', static::REFERENCES_COLUMN))
            ->add(Column::of('Criticality::scomp_id', static::CRITICALITY_SCOMP_ID_COLUMN))
            ->
            next()
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::of('${regulation_control:${regulation.scomp_id}:scomp_id}'))
            ->add(Column::empty())
            ->add(Column::of('${criticality.scomp_id}'));
    }

    public function title(): string
    {
        return "Findings";
    }

    protected function importRow(RowContext $row)
    {
        $finding = Finding::upsert(
            $row->nonEmpty(Column::SCOMP_ID_COLUMN),
            [
                'name' => $row[static::NAME_COLUMN],
                'description' => $row[static::DESCRIPTION_COLUMN],
                'criticality_id' => $row->ifPresent(static::CRITICALITY_SCOMP_ID_COLUMN, fn($value) => $this->compositeKeyContainer->get('criticality', $value)),
            ]);

        $this->compositeKeyContainer->set('finding', $finding->configurationItem->scomp_id, $finding->id);

        DB::delete("DELETE FROM finding_assigned WHERE finding_id = ?", [$finding->id]);

        $rawScomps = $row[static::CONTROL_SCOMP_ID_COLUMN];

        if (!empty($rawScomps)) {
            $scomps = explode(",", $rawScomps);
            foreach ($scomps as $scomp) {
                $parts = explode(":", $scomp);
                $scompType = $parts[0];

                $refId = $this->compositeKeyContainer->get($scompType, ... array_slice($parts, 1));

                if ($scompType == 'strategy_objective') {
                    $finding->objectives()->attach($refId);
                } elseif ($scompType == 'regulation_control') {
                    $finding->controls()->attach($refId);
                } else {
                    throw new \Exception("Unknown $scompType $scompType");
                }
            }

            $finding->save();
        }
    }
}

