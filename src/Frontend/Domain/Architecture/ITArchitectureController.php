<?php

namespace Swark\Frontend\Domain\Architecture;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Swark\Cms\Chapters\Chapters;
use Swark\Cms\Facades\Cms;
use Swark\DataModel\Enterprise\Domain\Repository\ITArchitectureRepository;

class ITArchitectureController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct(public readonly ITArchitectureRepository $architectureRepository)
    {
    }

    protected function createViewArgs(): array
    {
        return [
            'data_classifications' => \Swark\DataModel\Enterprise\Domain\Entity\DataClassification::all(),
            'zone_model' => \Swark\DataModel\Enterprise\Domain\Entity\Zone::with(['dataClassification', 'actors'])->get(),
            'c4_zone' => $this->createC4ZoneModel(),
            'matrix' => $this->architectureRepository->createZoneMatrix(),
        ];
    }

    public function index()
    {
        $chapters = Chapters::of([
            ['data-classification', __('swark::g.it_architecture.data_classification.title')],
            ['zone-model', __('swark::g.it_architecture.zone_model.title'), [
                ['zone-matrix', __('swark::g.it_architecture.matrix.title')]
            ]
            ],
        ]);

        Cms::commence(
            resourcePath: 'strategy__*',
            chapters: $chapters
        );

        return swark_view_auto($this->createViewArgs());
    }

    protected function createC4ZoneModel(): C4Generator
    {
        $r = new C4Generator();
        $data = \Swark\DataModel\Enterprise\Domain\Entity\Zone::with(['dataClassification', 'actors'])->get();

        foreach ($data as $model) {
            $r->push(<<<LINE
Boundary({$model->scomp_id}, "{$model->name}", "{$model->description}") {
}
LINE
            );
        }

        foreach ($data as $model) {
            foreach ($model->actors as $actor) {
                $r->pushOnce(<<<LINE
Person({$actor->scomp_id}, "{$actor->name}")
LINE
                );

                $r->pushOnce(<<<LINE
Rel({$actor->scomp_id}, "{$model->scomp_id}", "<<access to>>")
LINE
                );
            }
        }

        return $r;
    }
}
