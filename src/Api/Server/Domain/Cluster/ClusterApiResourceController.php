<?php

namespace Swark\Api\Server\Domain\Cluster;

use Illuminate\Routing\Controller;
use Swark\Api\Server\Internal\NamingContext;
use Swark\DataModel\InformationTechnology\Domain\Entity\Component\Cluster;

class ClusterApiResourceController extends Controller
{
    public function store(ClusterRequest $request): ClusterResource
    {

        $args = $request->toArray();

        $namingContext = NamingContext::of(Cluster::class,
            $args['id'] ?? null,
            $args['_namings'] ?? null,
        );

        $model = $namingContext->resolve();

        if (!$model) {
            $model = new Cluster();
        }

        $model->name = $args['name'];
        $model->save();

        $namingContext->attachCustomNamings($model);

        return new ClusterResource($model);
    }
}
