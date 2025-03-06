<?php

namespace Swark\Frontend\Presenter\InformationTechnology;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Swark\DataModel\Infrastructure\Eloquent\Model\Meta\ResourceType;
use Swark\DataModel\Infrastructure\Repository\InformationTechnology\Component\ResourceRepository;
use Swark\Kernel\Infrastructure\Facades\Cms;

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
