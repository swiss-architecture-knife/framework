<?php

namespace Swark\Frontend\Presenter\SoftwareArchitecture;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Criticality;
use Swark\DataModel\Infrastructure\Repository\SoftwareArchitecture\SoftwareRepository;

class CatalogController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct(public readonly SoftwareRepository $softwareRepository)
    {

    }

    public function index()
    {
        $viewArgs = [
            'criticality_range' => Criticality::findRange(),
            'softwares' => $this->softwareRepository->findSummary()->toArray(),
        ];

        return swark_view_auto($viewArgs);
    }
}
