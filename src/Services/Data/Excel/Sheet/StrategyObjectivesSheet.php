<?php

namespace Swark\Services\Data\Excel\Sheet;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Strategy\Domain\Entity\Objective;
use Swark\Services\Data\Excel\Column;
use Swark\Services\Data\Excel\Header;
use Swark\Services\Data\Excel\Import\RowContext;

class StrategyObjectivesSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle
{
    use Exportable;

    const NAME_COLUMN = 'name';
    const DESCRIPTION_COLUMN = 'description';
    const REASON_COLUMN = 'reason';
    const STRATEGY_SCOMP_ID_COLUMN = 'strategy_scomp_id';
    const ANSWERS_QUESTION_COLUMN = 'answers_questions';

    public function generator(): \Generator
    {
        yield ['name', 'scomp-id', 'description', 'reason', 'strategy-scomp-id'];
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Name', static::NAME_COLUMN))
            ->add(Column::scompId())
            ->add(Column::of('Description', static::DESCRIPTION_COLUMN))
            ->add(Column::of('Reason', static::REASON_COLUMN))
            ->add(Column::of('Strategy::scomp_id', static::STRATEGY_SCOMP_ID_COLUMN))
            ->add(Column::of('Strategy::scomp_id', static::ANSWERS_QUESTION_COLUMN))
            ->
            next()
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::of('Reference to Strategy::scomp_id'))
            ->add(Column::of('(${strategy_question:${strategy.scomp_id}.scomp_id} | ${regulation_chapter:${regulation.scomp_id}:external_id})*'))
            ;
    }

    public function title(): string
    {
        return "Strategy objectives";
    }

    protected function importRow(RowContext $row)
    {
        $refStrategyScompId = $row->nonEmpty(static::STRATEGY_SCOMP_ID_COLUMN);

        $objective = Objective::updateOrCreate([
            'scomp_id' => $row->nonEmpty(Column::SCOMP_ID_COLUMN),
        ], [
            'name' => $row[static::NAME_COLUMN],
            'description' => $row[static::DESCRIPTION_COLUMN],
            'reason' => $row[static::REASON_COLUMN],
            'strategy_id' => $this->compositeKeyContainer->get('strategy', $refStrategyScompId),
        ]);

        $rawScomps = $row[static::ANSWERS_QUESTION_COLUMN];

        if (!empty($rawScomps)) {
            $scomps = explode(",", $rawScomps);
            foreach ($scomps as $scomp) {
                $parts = explode(":", $scomp);
                $scompType = $parts[0];

                $refId = $this->compositeKeyContainer->get($scompType, ... array_slice($parts, 1));

                if ($scompType == 'strategy_question') {
                    $objective->questions()->attach($refId);
                } elseif ($scompType == 'regulation_chapter') {
                    $objective->regulationChapters()->attach($refId);
                } else {
                    throw new \Exception("Unknown $scompType $scompType");
                }
            }

            $objective->save();
        }


        $this->compositeKeyContainer->set('strategy_objective', $refStrategyScompId . ":" . $objective->scomp_id, $objective->id);
        $this->compositeKeyContainer->set('strategy_objective', $objective->scomp_id, $objective->id);
    }
}

