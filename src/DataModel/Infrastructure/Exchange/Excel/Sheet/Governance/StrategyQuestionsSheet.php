<?php

namespace Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Governance;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Strategy\Question;
use Swark\DataModel\Infrastructure\Exchange\Excel\Column;
use Swark\DataModel\Infrastructure\Exchange\Excel\Header;
use Swark\DataModel\Infrastructure\Exchange\Excel\RowContext;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\AbstractSwarkExcelSheet;

class StrategyQuestionsSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle
{
    use Exportable;

    const NAME_COLUMN = 'name';
    const STRATEGY_SCOMP_ID_COLUMN = 'strategy_scomp_id';

    public function generator(): \Generator
    {
        yield ['name', 'scomp-id'];
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Name', static::NAME_COLUMN))
            ->add(Column::scompId())
            ->add(Column::of('Strategy::scomp_id', static::STRATEGY_SCOMP_ID_COLUMN))
            ->
            next()
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::of('Multiple strategies, separated by comma'));
    }

    public function title(): string
    {
        return "Strategy questions";
    }

    protected function importRow(RowContext $row)
    {
        $question = Question::upsert(
            $row->nonEmpty(Column::SCOMP_ID_COLUMN),
            [
                'name' => $row[static::NAME_COLUMN],
            ]);

        $strategyScompId = $row->nonEmpty(static::STRATEGY_SCOMP_ID_COLUMN);

        $question->strategies()->attach($this->compositeKeyContainer->get('strategy', $strategyScompId));
        $question->save();

        $this->compositeKeyContainer->set('strategy_question', $strategyScompId . ":" . $question->configurationItem->scomp_id, $question->id);
    }
}

