<?php

namespace Swark\Frontend\Presenter\InformationTechnology;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Swark\Cms\Domain\Model\Chapters\Chapters;
use Swark\DataModel\Infrastructure\Repository\InformationTechnology\Component\BaremetalRepository;
use Swark\Kernel\Infrastructure\Facades\Cms;

class BaremetalController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct(public readonly BaremetalRepository $baremetalRepository)
    {

    }

    protected function createViewArgs(): array
    {
        return [
            'root' => $this->baremetalRepository->findGroupedBaremetals()->toArray(),
        ];
    }

    public function index()
    {
        $chapters = Chapters::of([
            ['summary', __('swark::g.infrastructure.baremetal.overview')],
            ['items', __('swark::g.infrastructure.baremetal.summary')],
        ]);

        Cms::commence(resourcePath: 'baremetal__*', chapters: $chapters);

        return swark_view_auto($this->createViewArgs());
    }
}
