<?php

namespace Swark\Frontend\Domain\Strategy;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Swark\Cms\Chapters\Chapters;
use Swark\Cms\Facades\Cms;
use Swark\DataModel\Content\Domain\Entity\Content;
use Swark\DataModel\Governance\Domain\Entity\Criticality;
use Swark\DataModel\Governance\Domain\Entity\Kpi\Period;
use Swark\DataModel\Governance\Domain\Entity\Strategy\Strategy;
use Swark\DataModel\Governance\Domain\Repository\StrategyRepository;

class StrategyController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct(public readonly StrategyRepository $strategyRepository)
    {

    }

    protected function getStrategy(): ?Strategy
    {
        return $strategy = \Swark\DataModel\Governance\Domain\Entity\Strategy\Strategy::latest()->first();
    }

    protected function createViewArgs($r = []): array
    {
        $strategy = $this->getStrategy();
        $findingsByObjective = $strategy ? $this->strategyRepository->findFindingsByObjective($strategy) : null;
        $findings = [];

        // create lookup table
        if ($findingsByObjective) {
            foreach ($findingsByObjective as $objective) {
                foreach ($objective->findings as $finding) {
                    if (!isset($findings['' . $finding->id])) {
                        $findings['' . $finding->id] = $finding;
                    }
                }
            }
        }

        $contents = Content::whereLike('scomp_id', 'strategy_%')->orWhereLike('scomp_id', 'company_%')->get();

        $criticalityRange = Criticality::findRange();

        return $r + [
                'contents' => $contents,
                'strategy_latest' => $this->getStrategy(),
                'findings_by_objective' => $findingsByObjective ?? [],
                'findings' => $findings,
                'criticality_range' => $criticalityRange,
            ];
    }

    public function index()
    {
        $chapters = Chapters::of([
            ['introduction', __('swark::g.company.introduction.title')],
            ['vision', __('swark::g.strategy.vision.title')],
            ['big_picture', __('swark::g.strategy.big_picture.title')],
            ['strategy', __('swark::g.strategy.title')],
        ]);

        Cms::commence(
            resourcePath: 'strategy__*',
            chapters: $chapters
        );

        return swark_view_auto($this->createViewArgs());
    }

    public function findings()
    {
        $viewArgs = $this->createViewArgs();

        $objectives = map_to_named_items($viewArgs['findings_by_objective']);

        $chapters = [
            ['overview', __('swark::g.findings.overview'),
                $objectives,
            ],
            ['details', __('swark::g.findings.actions_detail')],
            ['timeline', __('swark::g.findings.timeline')]
        ];

        $chapters = Chapters::of($chapters);

        Cms::commence(resourcePath: 'findings__*',
            chapters: $chapters,
        );

        return swark_view_auto($viewArgs);
    }

    public function kpi()
    {
        $strategy = $this->getStrategy();
        $period = Period::first();
        $kpis = $strategy && $period ? $this->strategyRepository->findKpisByStrategyAndPeriod($strategy, $period) : [];

        return swark_view('strategy.kpi', [
            'kpis' => $kpis,
        ]);
    }
}
