<?php

namespace Swark\Frontend\Domain\Software;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Swark\DataModel\Enterprise\Domain\Entity\Criticality;
use Swark\DataModel\Software\Domain\Repository\SoftwareRepository;

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
