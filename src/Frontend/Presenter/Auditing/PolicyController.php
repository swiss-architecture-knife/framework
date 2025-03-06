<?php

namespace Swark\Frontend\Presenter\Auditing;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Swark\DataModel\Infrastructure\Eloquent\Model\Auditing\Policy;
use Swark\Kernel\Infrastructure\Facades\Cms;

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
