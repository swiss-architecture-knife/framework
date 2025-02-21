<?php

use Illuminate\Support\Facades\Route;
use Swark\Api\Server\Domain\Baremetal\BaremetalApiResourceController;
use Swark\Api\Server\Domain\Cluster\ClusterApiResourceController;
use Swark\Api\Server\Domain\Host\HostApiResourceController;
use Swark\Api\Server\Domain\Namespace\NamespaceApiResourceController;
use Swark\Api\Server\Domain\Runtime\RuntimeApiResourceController;
use Swark\Api\Server\Domain\Software\SoftwareApiResourceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/
Route::apiResource('baremetals', BaremetalApiResourceController::class);
Route::apiResource('hosts', HostApiResourceController::class);
Route::apiResource('softwares', SoftwareApiResourceController::class);
Route::apiResource('clusters', ClusterApiResourceController::class);
Route::apiResource('namespaces', NamespaceApiResourceController::class);
Route::apiResource('runtimes', RuntimeApiResourceController::class);
