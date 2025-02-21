<?php

namespace Swark\Frontend\Domain\Policy;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Swark\Cms\Facades\Cms;
use Swark\DataModel\Policy\Domain\Entity\Policy;

class PolicyController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function detail(Policy $policy)
    {
        $chapters = [
            ['rules', __('swark::policy.rules'), map_to_named_items($policy->rules)],
        ];

        Cms::commence(
            resourcePath: 'policy__' . $policy->id . '__*',
            chapters: $chapters,
        );

        return swark_view_auto([
            'policy' => $policy,
            'rules' => $policy->rules(),
        ]);
    }
}
