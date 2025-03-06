<?php

namespace Swark\DataModel\Presenter\API\InformationTechnology\Component;

use Illuminate\Routing\Controller;
use Swark\DataModel\Presenter\API\InformationTechnology\Component\Dto\NamespaceRequest;
use Swark\DataModel\Presenter\API\InformationTechnology\Component\Dto\NamespaceResource;
use Swark\DataModel\Presenter\API\NamingContext;

class NamespaceApiResourceController extends Controller
{
    public function store(NamespaceRequest $request): NamespaceResource
    {
        $args = $request->toArray();

        $namingContext = NamingContext::of(NamespaceRequest::class,
            $args['id'] ?? null,
            $args['_namings'] ?? null,
        );
        $model = $namingContext->resolve();

        if (!$model) {
            $model = new NamespaceResource([]);
        }

        $model->name = $args['name'];
        $model->cluster_id = $request->cluster->id;

        $model->save();

        return new NamespaceResource($model);
    }
}
