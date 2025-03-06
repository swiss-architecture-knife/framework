<?php

namespace Swark\DataModel\Presenter\API\InformationTechnology\Component;

use Illuminate\Routing\Controller;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Runtime;
use Swark\DataModel\Presenter\API\InformationTechnology\Component\Dto\RuntimeRequest;
use Swark\DataModel\Presenter\API\InformationTechnology\Component\Dto\RuntimeResource;
use Swark\DataModel\Presenter\API\NamingContext;

class RuntimeApiResourceController extends Controller
{
    public function store(RuntimeRequest $request)
    {
        $args = $request->toArray();

        $namingContext = NamingContext::of(Runtime::class,
            $args['id'] ?? null,
            $args['_namings'] ?? []
        );

        $model = $namingContext->resolve();

        if (!$model) {
            $model = new Runtime();
        }

        $model->name = $args['name'];
        $model->host_id = $request->host->id;
        $model->release_id = $request->release->id;
        $model->save();

        $namingContext->attachCustomNamings($model);

        return new RuntimeResource($model);
    }
}
