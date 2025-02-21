<?php

namespace Swark\Frontend\Domain\Infrastructure;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Swark\Cms\Chapters\Chapters;
use Swark\Cms\Facades\Cms;
use Swark\DataModel\Infrastructure\Domain\Repository\BaremetalRepository;

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
