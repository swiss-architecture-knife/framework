<?php

namespace Swark\DataModel\Presenter\API\InformationTechnology\Component;

use Illuminate\Routing\Controller;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Cluster;
use Swark\DataModel\Presenter\API\InformationTechnology\Component\Dto\ClusterRequest;
use Swark\DataModel\Presenter\API\InformationTechnology\Component\Dto\ClusterResource;
use Swark\DataModel\Presenter\API\NamingContext;

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
