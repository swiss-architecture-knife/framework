<?php

use Illuminate\Support\Facades\Route;
use Swark\DataModel\Presenter\API\InformationTechnology\Component\BaremetalApiResourceController;
use Swark\DataModel\Presenter\API\InformationTechnology\Component\ClusterApiResourceController;
use Swark\DataModel\Presenter\API\InformationTechnology\Component\HostApiResourceController;
use Swark\DataModel\Presenter\API\InformationTechnology\Component\NamespaceApiResourceController;
use Swark\DataModel\Presenter\API\InformationTechnology\Component\RuntimeApiResourceController;
use Swark\DataModel\Presenter\API\InformationTechnology\Component\SoftwareApiResourceController;

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
