<?php

namespace Swark\Api\Server\Domain\Runtime;

use Illuminate\Routing\Controller;
use Swark\Api\Server\Internal\NamingContext;
use Swark\DataModel\InformationTechnology\Domain\Entity\Component\Runtime;

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
