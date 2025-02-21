<?php

namespace Swark\Frontend\Domain;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Swark\Cms\Chapters\Chapters;
use Swark\Cms\Facades\Cms;

class SandboxController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index()
    {
        return swark_view('sandbox.index', [
        ]);
    }

    public function withOneArg(string $first)
    {
        return swark_view('sandbox.one-arg', [
        ]);
    }

    public function withTwoArgs(string $second, string $first)
    {
        return swark_view('~', ['arg' => 'test'
        ]);
    }

    public function sadChap1(string $sadId)
    {
        $chapters = Chapters::of([
            ['requirements-overview', 'Requirements overview'],
            ['quality-goals', 'Quality goals', [
                ['a', 'FFFFFFF']
            ]],
            ['stakeholder', 'Stakeholder'],
        ]);

        Cms::commence(
            resourcePath: 'sandbox__s-ad__*',
            chapters: $chapters,
        );

        return swark_view_auto([
            'variableInView' => 'This is a content of a variable, defined in a view',
        ]);
    }
}
