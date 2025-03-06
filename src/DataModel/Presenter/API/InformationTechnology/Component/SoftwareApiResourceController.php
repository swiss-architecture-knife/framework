<?php

namespace Swark\DataModel\Presenter\API\InformationTechnology\Component;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture\Software;
use Swark\DataModel\Presenter\API\InformationTechnology\Component\Dto\SoftwareCollection;
use Swark\DataModel\Presenter\API\InformationTechnology\Component\Dto\SoftwareRequest;
use Swark\DataModel\Presenter\API\InformationTechnology\Component\Dto\SoftwareResource;
use Swark\DataModel\Presenter\API\NamingContext;

class SoftwareApiResourceController extends Controller
{
    public function index(Request $request)
    {
        $args = $request->toArray();
        $query = Software::with(['releases']);

        if (isset($args['q'])) {
            $query->whereAny(['scomp_id', 'name'], 'LIKE', $args['q'] . '%');
        }

        $data = $query->orderBy('name')->get();

        return new SoftwareCollection($data);
    }

    public function store(SoftwareRequest $request)
    {
        $args = $request->toArray();

        $namingContext = NamingContext::of(Software::class,
            $args['id'] ?? null,
            $args['_namings'] ?? []
        );

        $model = $namingContext->resolve();

        if (!$model) {
            $model = new Software();
        }

        $model->name = $args['name'];
        $model->is_runtime = $args['is_runtime'] ?? false;
        $model->is_operating_system = $args['is_operating_system'] ?? false;
        $model->save();

        $namingContext->attachCustomNamings($model);

        return new SoftwareResource($model);
    }

    public function show(Request $request)
    {

    }
}
