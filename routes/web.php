<?php

use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;
use Swark\Frontend\Domain\Architecture\ITArchitectureController;
use Swark\Frontend\Domain\GlossaryController;
use Swark\Frontend\Domain\Infrastructure\BaremetalController;
use Swark\Frontend\Domain\Infrastructure\ClusterController;
use Swark\Frontend\Domain\Infrastructure\InfrastructureController;
use Swark\Frontend\Domain\Infrastructure\ResourceController;
use Swark\Frontend\Domain\Policy\PolicyController;
use Swark\Frontend\Domain\SandboxController;
use Swark\Frontend\Domain\Software\CatalogController;
use Swark\Frontend\Domain\Strategy\StrategyController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::name('swark.')
    ->prefix('swark')
    // Use SubstituteBindings so we can find components by their ID
    ->middleware([SubstituteBindings::class, \Swark\Frontend\Infrastructure\Routing\Middleware\RegisterRoutableConfigurationItems::class])
    ->group(function () {
        Route::domain(null)
            ->group(function () {
                Route::prefix('strategy')
                    ->name('strategy.')->group(function () {
                        Route::get('/big-picture', [StrategyController::class, 'bigPicture'])->name('big-picture');
                        Route::get('/overview', [StrategyController::class, 'index'])->name('index');
                        Route::get('/findings', [StrategyController::class, 'findings'])->name('findings');
                        Route::get('/kpi', [StrategyController::class, 'kpi'])->name('kpi');
                    });

                Route::prefix('it-architecture')
                    ->name('it_architecture.')->group(function () {
                        Route::get('/', [ITArchitectureController::class, 'index'])->name('index');
                    });

                Route::prefix('infrastructure')
                    ->name('infrastructure.')->group(function () {
                        Route::get('/system-landscape', [InfrastructureController::class, 'index'])->name('index');
                        Route::get('/baremetals', [BaremetalController::class, 'index'])->name('baremetal.index');
                        Route::get('/clusters', [ClusterController::class, 'index'])->name('cluster.index');
                        Route::get('/clusters/{cluster}', [ClusterController::class, 'detail'])->name('cluster.detail');
                        Route::get('/resources/{resource_type}', [ResourceController::class, 'index'])->name('resource.index');
                    });

                Route::prefix('software')
                    ->name('software.')->group(function () {
                        Route::get('/overview', [CatalogController::class, 'index'])->name('index');
                        Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');

                    });
                Route::prefix('policies')
                    ->name('policies.')->group(function () {
                        Route::get('/{policy}', [PolicyController::class, 'detail'])->name('detail');
                    });

                Route::get('/glossary', [GlossaryController::class, 'index'])->name('glossary.index');

                Route::prefix('sandbox')
                    ->name('sandbox.')->group(function() {
                        Route::get('/', [SandboxController::class, 'index'])->name('index');
                        Route::get('/s-ad/{sadId}/chapter-1', [SandboxController::class, 'sadChap1'])->name('s-ad.chapter-1');
                    });
            });


        Route::name('entrypoint')->get('/', fn() => redirect()->route('swark.strategy.index'));
    });
