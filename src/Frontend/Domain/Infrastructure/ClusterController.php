<?php

namespace Swark\Frontend\Domain\Infrastructure;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Swark\Cms\Chapters\Chapters;
use Swark\Cms\Facades\Cms;
use Swark\DataModel\Deployment\Domain\Repository\ApplicationInstanceRepository;
use Swark\DataModel\Infrastructure\Domain\Entity\Cluster;
use Swark\DataModel\Infrastructure\Domain\Repository\ClusterRepository;

class ClusterController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct(
        public readonly ClusterRepository $clusterRepository,
        public readonly ApplicationInstanceRepository $applicationInstanceRepository)
    {
    }

    public function index()
    {
        // no CMS commencing needed here

        return swark_view_auto([
            'items' => $this->clusterRepository->findClusterSummary(),
        ]);
    }

    public function detail(Cluster $cluster)
    {
        $applicationInstanceItems = $this->applicationInstanceRepository->findApplicationInstances($cluster->id)->toArray();

        // TODO Namespaces, Baremetal and Runtimes are currently missing

        $chapters = Chapters::of([
            ['overview', __('swark::cluster.overview.title')],
            ['application-instances', __('swark::application_instance.title')],
        ]);

        Cms::commence(
            resourcePath: 'cluster__' . $cluster->id . '__*',
            chapters: $chapters
        );

        return swark_view_auto([
            'cluster' => $cluster,
            'id' => $cluster->id,
            'application_instance_items' => $applicationInstanceItems,
        ]);
    }
}
