<?php

namespace Swark\Api\Server\Domain\Software;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Swark\Api\Server\Internal\NamingContext;
use Swark\DataModel\Software\Domain\Entity\Software;

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
