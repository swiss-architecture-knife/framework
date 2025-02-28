<?php

namespace Swark\Services\Data\Excel\Sheet;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Auditing\Domain\Entity\Action;
use Swark\Services\Data\Excel\Column;
use Swark\Services\Data\Excel\Header;
use Swark\Services\Data\Excel\Import\RowContext;

class ActionsSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle
{
    use Exportable;

    const NAME_COLUMN = 'name';
    const DESCRIPTION_COLUMN = 'description';
    const CRITICALITY_SCOMP_ID_COLUMN = 'criticality_id';
    const ACTIONABLE_SCOMP_ID_COLUMN = 'actionable_scomp_id';
    const REFERENCES_COLUMN = 'references';
    const BEGIN_AT_COLUMN = 'begin_at';
    const END_AT_COLUMN = 'end_at_or_duration';

    public function generator(): \Generator
    {
        yield ['name', 'scomp-id', 'description', 'actionable-scomp-id', 'references', 'begin at', 'end at'];
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Name', static::NAME_COLUMN))
            ->add(Column::scompId())
            ->add(Column::of('Description', static::DESCRIPTION_COLUMN))
            ->add(Column::of('Actionable::scomp_id', static::ACTIONABLE_SCOMP_ID_COLUMN))
            ->add(Column::of('References', static::REFERENCES_COLUMN))
            ->add(Column::of('Begin at', static::BEGIN_AT_COLUMN))
            ->add(Column::of('End at / Duration', static::END_AT_COLUMN))
            ->
            next()
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::of('${strategy_objective:${strategy.scomp_id}:scomp_id}'))
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::of('e.g. 1w or 3 months'));
    }

    public function title(): string
    {
        return "Actions";
    }

    protected function importRow(RowContext $row)
    {
        $action = Action::updateOrCreate([
            'scomp_id' => $row->nonEmpty(Column::SCOMP_ID_COLUMN),
        ], [
            'name' => $row[static::NAME_COLUMN],
            'description' => $row[static::DESCRIPTION_COLUMN],
            'begin_at' => $row->dateOrNull(static::BEGIN_AT_COLUMN),
            'end_at' => $row->ifPresent(static::END_AT_COLUMN, function ($value) use ($row) {
                if (preg_match('/^(\d+)(\s*)w$/', $value, $ret)) {
                    $value = $ret[1] . " weeks";
                }

                if ($date = $row->dateOrNull(static::BEGIN_AT_COLUMN)) {
                    /** @var $date Carbon */
                    return $date->add($value);
                }

                return null;
            }),
        ]);

        $this->compositeKeyContainer->set('action', $action->scomp_id, $action->id);

        DB::delete("DELETE FROM action_assigned WHERE action_id = ?", [$action->id]);

        $rawScomps = $row[static::ACTIONABLE_SCOMP_ID_COLUMN];

        if (!empty($rawScomps)) {
            $scomps = explode(",", $rawScomps);
            foreach ($scomps as $scomp) {
                $parts = explode(":", $scomp);
                $scompType = $parts[0];

                $refId = $this->compositeKeyContainer->get($scompType, ... array_slice($parts, 1));

                if ($scompType == 'strategy_objective') {
                    $action->objectives()->attach($refId);
                } elseif ($scompType == 'regulation_control') {
                    $action->controls()->attach($refId);
                } elseif ($scompType == 'finding') {
                    $action->findings()->attach($refId);
                } else {
                    throw new \Exception("Unknown $scompType $scompType");
                }
            }

            $action->save();
        }
    }
}

