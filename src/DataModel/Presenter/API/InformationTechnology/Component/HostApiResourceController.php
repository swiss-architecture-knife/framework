<?php

namespace Swark\DataModel\Presenter\API\InformationTechnology\Component;

use Illuminate\Routing\Controller;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Host;
use Swark\DataModel\Presenter\API\InformationTechnology\Component\Dto\HostRequest;
use Swark\DataModel\Presenter\API\InformationTechnology\Component\Dto\HostResource;
use Swark\DataModel\Presenter\API\NamingContext;

class HostApiResourceController extends Controller
{
    public function store(HostRequest $request)
    {
        $args = $request->toArray();

        $namingContext = NamingContext::of(Host::class,
            $args['id'] ?? null,
            $args['_namings'] ?? []
        );

        $model = $namingContext->resolve();

        if (!$model) {
            $model = new Host();
        }

        $model->name = $args['name'];
        $model->operating_system_id = $request->operatingSystem->id;
        $model->virtualizer_id = $request->virtualizer?->id;
        $model->parent_host_id = $request->parentHost?->id;
        $model->baremetal_id = $request->baremetal?->id;
        $model->save();

        $namingContext->attachCustomNamings($model);

        return new HostResource($model);
    }
}
