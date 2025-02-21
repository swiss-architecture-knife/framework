<?php
declare(strict_types=1);

namespace Swark;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\View\Factory;
use Illuminate\View\View;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\Navigation\Navigation;
use Spatie\Navigation\Section;
use Swark\Cms\Cms;
use Swark\Cms\Content\NotFoundResponder;
use Swark\Cms\Events\DelegatingHooksRegistrar;
use Swark\Cms\Events\RenderContentOnHooksRegistrar;
use Swark\Cms\Page\PageFactory;
use Swark\Cms\Store\DatabaseStore;
use Swark\Cms\Store\FilesystemStore;
use Swark\Cms\Store\Search\Searchable;
use Swark\Console\Commands\ImportKubernetesCluster;
use Swark\Console\Commands\ImportStamdataCommand;
use Swark\Console\Commands\IngestFlatStructure;
use Swark\Console\Commands\ProcessBatchDependencies;
use Swark\Console\Commands\ReviewKubernetesTokensCommand;
use Swark\Console\Commands\UpdateHelmVersions;
use Swark\Content\Domain\Service\MarkdownService;
use Swark\Content\Infrastructure\Facades\Markdown;
use Swark\DataModel\Ecosystem\Domain\Entity\ResourceType;
use Swark\DataModel\ModelTypes;
use Swark\DataModel\Policy\Domain\Entity\Policy;
use Swark\Frontend\Infrastructure\View\RoutableConfigurationItem;
use Swark\Frontend\Infrastructure\View\RoutableViewFinder;
use Swark\Frontend\UI\Components\Block\Content;
use Swark\Frontend\UI\Components\Block\Resolve;
use Swark\Frontend\UI\Components\Chapter\Chapter;
use Swark\Frontend\UI\Components\Chapter\ChapterHeader;
use Swark\Frontend\UI\Components\Chapter\Label;
use Swark\Frontend\UI\Components\Criticality;
use Swark\Frontend\UI\Components\Diagram\Diagram;
use Swark\Frontend\UI\Components\Menu;
use Swark\Frontend\UI\Components\MermaidJs;
use Swark\Frontend\UI\Components\Outline\Outline;
use Swark\Frontend\UI\Components\Plantuml;
use Swark\Frontend\UI\Components\Plotly;
use TorMorten\Eventy\Facades\Eventy;

class SwarkServiceProvider extends PackageServiceProvider
{
    const NAMESPACE_PREFIX = 'Swark\\';
    const PACKAGE_PREFIX = 'swark';

    public function configurePackage(Package $package): void
    {
        $package->name('swark')
            ->hasRoutes('web')
            ->hasTranslations()
            ->hasMigrations([
                '0001_01_01_000000_create_users_table',
                '0001_01_01_000001_create_cache_table',
                '0001_01_01_000002_create_jobs_table',
                'create_swark_tables'
            ])
            ->runsMigrations()
            ->hasConfigFile('swark')
            ->hasAssets()
            ->hasViews('swark')
            ->hasViewComposer(swark_resource('layout'), $this->navigation())
            ->hasViewComponents('swark',
                Criticality::class,
                Menu::class,
                MermaidJs::class,
                Plantuml::class,
                Plotly::class,
                Diagram::class,
                // ComponentTagCompiler ignores the namespace or root. Instead ${prefix}-${classname} is used for resolving by alias
                // x-swark-label
                Label::class,
                Content::class,
                // x-swark-chapter
                Chapter::class,
                // x-swark-header
                ChapterHeader::class,
                // x-swark-online
                Outline::class,
                // x-swark-resolve
                Resolve::class,
            )
            ->hasConsoleCommands(
                ImportStamdataCommand::class,
                IngestFlatStructure::class,
                ImportKubernetesCluster::class,
                UpdateHelmVersions::class,
                ProcessBatchDependencies::class,
                ReviewKubernetesTokensCommand::class,
            );
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(MarkdownService::class);
        $this->app->alias(MarkdownService::class, Markdown::ALIAS);

        // Root aliases in Blade have to be manually registered. app->alias only works in PHP
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias("Markdown", Markdown::class);

    }

    public function bootingPackage()
    {
        Route::bind('resource_type', function ($value) {
            // because of the auto-login, we can not use "person" with bySecuredGuid as the user is not logged in yet
            return ResourceType::where('id', $value)->firstOrFail();
        });

        $this->configureCms();
        $this->configureViews();
        $this->configureEloquent();
        $this->enableDatabaseLogging();
        $this->configureLogging();
    }

    /**
     * Configure local content and CMS
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function configureCms()
    {
        $this->app->singleton(FilesystemStore::class, function (Application $app) {
            return new FilesystemStore(config('swark.content.path'));
        });

        $this->app->singleton(PageFactory::class, fn() => new PageFactory());

        $this->app->when(Cms::class)
            ->needs(Searchable::class)
            ->give([
                DatabaseStore::class,
                FilesystemStore::class,
                NotFoundResponder::class
            ]);

        // CMS
        $this->app->bind('cms', Cms::class);
        // provide mappings from current route to local file items. It enforces the directory layout
        $this->app->singleton(RoutableConfigurationItem::class, fn() => new RoutableConfigurationItem());
        // provide a finder specifically for all Git-versionized/static content
        $this->app->singleton('content.finder', function ($app) {
            return new RoutableViewFinder(
                $app->make(RoutableConfigurationItem::class),
                new Filesystem(),
                [$app['config']['swark.content.path']],
                ['blade.php', 'md', 'html']
            );
        });

        // use #[FirstTagged('content-finder)] (or #[Tagged()] to resolve this instance
        $this->app->tag('content.finder', 'content-finder');

        // swark has its custom View factory so it can load content (Markdown, Blade, HTML) from the local filesystem, outside the actual application
        $this->app->bind('content.view-factory', function ($app) {
            return new Factory(
                $app->make('view.engine.resolver'),
                $app->make('content.finder'),
                $app->make(Dispatcher::class)
            );
        });

        // registrar for splatting/delegating hooks to its current route name. It makes it easier for the users to react to different content
        $this->app->singleton(DelegatingHooksRegistrar::class, function ($app) {
            return new DelegatingHooksRegistrar(
                $app->make('content.finder'),
                $app->make(RoutableConfigurationItem::class),
                $app['config']['swark.events.hookable'],
                );
        });

        // If present, render local files based upon the current shown configuration item, chapter and hook
        $this->app->bind('cms.render-on-hooks.registrar', fn($app) => new RenderContentOnHooksRegistrar($app['config']['swark.events.hookable'], $app->make('content.finder')));
        $this->app->make('cms.render-on-hooks.registrar')->register();
    }

    protected function configureViews()
    {
        $this->app->bind('view.finder', function ($app) {
            return new RoutableViewFinder(
                $app->make(RoutableConfigurationItem::class),
                $app['files'],
                $app['config']['view.paths']
            );
        });
    }

    protected function enableDatabaseLogging()
    {
        DB::listen(function ($query) {
            Log::info(
                $query->sql,
                $query->bindings,
            );
        });
    }

    protected function configureEloquent()
    {
        Relation::enforceMorphMap(ModelTypes::toMap());

        Builder::macro('whereLike', function ($column, $search) {
            return $this->where($column, 'LIKE', "%{$search}%");
        });

        Builder::macro('orWhereLike', function ($column, $search) {
            return $this->orWhere($column, 'LIKE', "%{$search}%");
        });
    }

    private function newNavigation(): Navigation
    {
        return app()->build(Navigation::class);
    }

    protected function navigation()
    {
        return function (View $view) {
            $view->with('top_navigation',
                $this->newNavigation()
                    ->add(__('swark::g.nav.top.start'), route('swark.strategy.index'))
                    ->add(__('swark::g.nav.top.admin'), route('filament.admin.pages.dashboard'))
                    ->tree()
            )->with('side_navigation',
                $this->newNavigation()
                    ->add(__('swark::g.nav.side.strategy.title'), route('swark.strategy.index'), fn(Section $section) => $section
                        ->add(__('swark::g.nav.side.strategy.overview'), route('swark.strategy.index'))
                        ->add(__('swark::g.nav.side.strategy.findings'), route('swark.strategy.findings'))
                        ->add(__('swark::g.nav.side.strategy.kpi'), route('swark.strategy.kpi'))
                    )
                    // no navigation for policy overview yet
                    ->add(__('swark::g.nav.side.policies'), '#', function (Section $section) {
                        $policies = Policy::all()->each(fn(Policy $item) => $section->add($item->name, route('swark.policies.detail', $item->id)));
                    })
                    ->add(__('swark::g.nav.side.it_architecture.title'), route('swark.it_architecture.index'))
                    ->add(__('swark::g.nav.side.infrastructure.title'), route('swark.infrastructure.index'), fn(Section $section) => $section
                        ->add(__('swark::g.nav.side.infrastructure.overview'), route('swark.infrastructure.index'))
                        ->add(__('swark::g.nav.side.infrastructure.baremetal'), route('swark.infrastructure.baremetal.index'))
                        ->add(__('swark::g.nav.side.infrastructure.cluster'), route('swark.infrastructure.cluster.index'))
                        ->add(__('swark::g.nav.side.infrastructure.resources'), '/infrastructure/resource', function (Section $section) {
                            $resources = ResourceType::inUse()->get()->each(fn(ResourceType $item) => $section->add($item->name, route('swark.infrastructure.resource.index', $item)));
                        })
                    )
                    ->add(__('swark::g.nav.side.software.title'), route('swark.software.index'), fn(Section $section) => $section
                        ->add(__('swark::g.nav.side.software.catalog'), route('swark.software.catalog'))
                    )
                    ->add(__('swark::g.nav.side.glossary.title'), route('swark.glossary.index'), fn(Section $section) => $section)
                    ->add(__('swark::g.nav.side.sandbox.title'), route('swark.sandbox.index'))
                    ->tree()
            );
        };
    }

    protected function configureNavigation()
    {
        ViewFacade::composer(swark_resource('layout'), $this->navigation());
    }

    protected function configureLogging()
    {
        // customize the label of a chapter. it also applies to the ToC. ToC can be identified by the Label's optional context
        /*
        Eventy::addFilter('chapter.label', function(array $args, Label $labelComponent) {
            $args[0] = '::: ';

            if ($labelComponent->context == 'toc') {
                $args[0] = '!!! ';
            }

            return $args;
        }, 10, 2);
        //*/

        // customize the header of a chapter
        /*
        Eventy::addFilter('chapter.header', function(array $args, Header $headerComponent) {
            $args['tag'] = 'span';
            return $args;
        }, 10, 2);
         //*/

        Eventy::addFilter('chapter.context', function (array $args) {
            $chapter = $args['chapter'];
            $context = $args['context'];

            return $context . 'test';
        }, 10, 2);

        Eventy::addFilter('chapter.before', function (array $args) {
            $chapter = $args['chapter'];
            $context = $args['context'];

            echo $context;
        }, 10, 2);

        // inject custom variable in a chapter
        ///*
        Eventy::addFilter('block.args', function (array $args, Resolve $resolveChapterComponent) {
            $args['test'] = $resolveChapterComponent->chapter->id;
            return $args;
        }, 10, 2);
        //*/

        Eventy::addAction('yo.*', function ($tagDepth, $level, $message, $args, $tag) {
            $padding = str_repeat('  ', $tagDepth - 2);
            $message = $padding . $message;

            match ($level) {
                "info" => Log::info($message),
                "error" => Log::error($message),
                "warn" => Log::warning($message),
                "debug" => Log::debug($message),
            };
        }, 1, 5);
    }
}
