<?php

namespace Swark\Frontend\Domain\Infrastructure;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Swark\Cms\Facades\Cms;
use Swark\DataModel\InformationTechnology\Domain\Repository\Component\ResourceRepository;
use Swark\DataModel\Meta\Domain\Entity\ResourceType;

class ResourceController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct(public readonly ResourceRepository $resourceRepository)
    {

    }

    public function index(ResourceType $resourceType)
    {
        $args = [
            'resource_type' => $resourceType,
            'root' => $this->resourceRepository->findGroupedResourcesOfType($resourceType->id)->toArray(),
        ];

        Cms::commence(resourcePath: 'resource_type__' . $resourceType->id . '__*', chapters: []);

        return swark_view_auto($args);
    }
}
