<?php

namespace Swark\Api\Server\Domain\Baremetal;

use Illuminate\Routing\Controller;
use Swark\Api\Server\Internal\NamingContext;
use Swark\DataModel\Cloud\Entity\ManagedBaremetal;
use Swark\DataModel\InformationTechnology\Domain\Entity\Component\Baremetal;

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
