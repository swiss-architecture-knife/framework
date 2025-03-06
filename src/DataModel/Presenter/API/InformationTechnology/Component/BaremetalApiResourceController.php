<?php

namespace Swark\DataModel\Presenter\API\InformationTechnology\Component;

use Illuminate\Routing\Controller;
use Swark\DataModel\Cloud\Entity\ManagedBaremetal;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Baremetal;
use Swark\DataModel\Presenter\API\InformationTechnology\Component\Dto\BaremetalRequest;
use Swark\DataModel\Presenter\API\InformationTechnology\Component\Dto\BaremetalResource;
use Swark\DataModel\Presenter\API\NamingContext;

class BaremetalApiResourceController extends Controller
{
    public function store(BaremetalRequest $request): BaremetalResource
    {
        $args = $request->toArray();

        $namingContext = NamingContext::of(Baremetal::class,
            $args['id'] ?? null,
            $args['_namings'] ?? null,
        );
        $model = $namingContext->resolve();

        if (!$model) {
            $model = new Baremetal();
        }

        $model->name = $args['name'];
        $model->save();

        $namingContext->attachCustomNamings($model);

        // if account has been provided, make this a managed baremetal instance
        if ($request->account) {
            if (!$model->managed) {
                $model->managed = new ManagedBaremetal();
            }

            $model->managed->baremetal_id = $model->id;
            $model->managed->managed_account_id = $request->account->id;
            $model->managed->availability_zone_id = $request->availabilityZone->id;
            $model->managed->managed_offer_id = 2;
            $model->managed->save();
        }

        return new BaremetalResource($model);
    }
}
