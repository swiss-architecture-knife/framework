<?php

namespace Swark\Frontend\Domain\Infrastructure;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class InfrastructureController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index()
    {
        return swark_view_auto();
    }
}
