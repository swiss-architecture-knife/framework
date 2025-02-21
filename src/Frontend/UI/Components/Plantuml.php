<?php

namespace Swark\Frontend\UI\Components;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\View\FileViewFinder;
use Jawira\PlantUmlToImage\Format;
use Jawira\PlantUmlToImage\PlantUml as PlantUmlExporter;
use Swark\Frontend\Infrastructure\View\RoutableConfigurationItem;
use Swark\Internal\Container\Attributes\FirstTagged;

class Plantuml extends Component
{
    private array $defaultExtensions = ['plantuml', 'txt'];

    public function __construct(
        #[FirstTagged('content-finder')]
        public readonly FileViewFinder            $fileViewFinder,
        public readonly RoutableConfigurationItem $routableComponents,
    ) {
    }

    public function render()
    {
        return function (array $data) {
            $extensions = $this->defaultExtensions;

            // custom file extension
            if (isset($data['attributes']['extension'])) {
                $extensions = [$data['attributes']['extension']];
            }

            $id = $data['attributes']['id'];

            $enableCache = isset($data['attributes']['caching']) ? filter_var($data['attributes']['caching'], FILTER_VALIDATE_BOOLEAN) : true;
            $doTrimming = (bool)($data['attributes']['trim']) ?? true;

            $routableComponents = $this->routableComponents;

            // transformer is called when a diagram has to be transformed from text to PNG/*
            $transformer = function (Diagram\Source $source) use ($enableCache, $id, $doTrimming, $routableComponents) {
                // TODO make disk configurable
                $disk = Storage::disk('public');
                $pathPrefix = 'diagrams/' . $routableComponents->getRelativeComponentPath(false) . '/' . $id;
                $imagePath = $pathPrefix . "_plantuml.png";
                $hashPath = $pathPrefix . '.sha';

                $recreateImage = false;

                if (!$disk->exists($imagePath) || !$enableCache) {
                    $recreateImage = true;
                } else if ($disk->exists($hashPath) && $disk->get($hashPath) != $source->hash()) {
                    $recreateImage = true;
                }

                $content = $source->content;

                // add required tags if not present
                if (!Str::contains('@startuml', $content)) {
                    $content = '@startuml' . PHP_EOL . $content;
                }

                if (!Str::contains('@enduml', $content)) {
                    $content .= PHP_EOL . '@enduml';
                }

                $error = null;

                try {
                    // recreate the image
                    if ($recreateImage) {
                        $plantUml = new PlantUmlExporter();
                        $png = $plantUml->convertTo($content, Format::PNG);
                        $disk->put($imagePath, $png, 'public');
                        $disk->put($hashPath, $source->hash(), 'public');
                    }
                } catch (\Exception $e) {
                    $error = $e;
                }

                return new Diagram\Output(
                    content: $doTrimming ? preg_replace('/^ +/m', '', $content) : $content,
                    url: Storage::url($imagePath),
                    error: $error
                );
            };

            $fallbackContent = $data['slot'];
            $locator = new Diagram\Locator(
                fileViewFinder: $this->fileViewFinder,
                transformer: new Diagram\Transformer($transformer),
                extensions: $extensions
            );

            $diagram = $locator->find(id: $id, fallbackContent: $fallbackContent);

            return view('swark::components.plantuml', [
                'diagram' => $diagram,
                'id' => 'plant-' . $id,
            ]);
        };
    }
}
