<?php

namespace Swark\Api\Server\Domain\Namespace;

use Illuminate\Routing\Controller;
use Swark\Api\Server\Internal\NamingContext;

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
