<?php

namespace Swark\Frontend\Presenter;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Swark\Cms\Domain\Model\Chapters\Chapters;
use Swark\Kernel\Infrastructure\Facades\Cms;

class GlossaryController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index()
    {
        $chapters = Chapters::of([
            ['swark', 'swark'],
            ['nis2', 'NIS2']
        ]);

        Cms::commence('glossary__*', chapters: $chapters);

        return swark_view_auto([],'glossary.index');
    }
}
