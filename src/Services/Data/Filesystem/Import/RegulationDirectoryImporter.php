<?php

namespace Swark\Services\Data\Filesystem\Import;

use DirectoryIterator;
use Swark\DataModel\Compliance\Domain\Entity\Chapter;
use Swark\DataModel\Compliance\Domain\Entity\Control;
use Swark\DataModel\Compliance\Domain\Entity\Regulation;
use Swark\Services\Data\CompositeKeyContainer;
use Swark\Services\Data\Importable;
use Symfony\Component\Yaml\Yaml;

/**
 * Imports regulations, regulation chapters and controls
 */
class RegulationDirectoryImporter implements Importable
{
    const DATA_YAML = 'data.yaml';

    const CONTROLS_DIRECTORY = 'controls';

    public function __construct(public readonly \SplFileInfo          $baseDirectory,
                                public readonly CompositeKeyContainer $compositeKeyContainer,
    )
    {
    }

    /**
     * Reads a YAML file from regulations/${scomp_id}/data.yaml and returns its value
     * @return array
     */
    protected function readYamlInformation(): array
    {
        $fileInfo = new \SplFileInfo($this->baseDirectory->getRealPath() . '/' . static::DATA_YAML);

        if (!$fileInfo->isFile()) {
            return [];
        }

        return Yaml::parseFile($fileInfo->getRealPath());
    }

    public function import()
    {
        $regulationScompId = $this->baseDirectory->getFilename();
        // in case a data.yaml is present
        $dataRef = ['scomp_id' => $regulationScompId, 'name' => $regulationScompId];
        $yaml = $this->readYamlInformation();

        $dataRef = array_merge($dataRef, $yaml);

        // update or create regulation based upon its scomp_id
        $regulation = Regulation::updateOrCreate(
            ['scomp_id' => $dataRef['scomp_id']],
            [
                'name' => $dataRef['name']
            ]
        );

        yo_info('Regulation %s upserted', [$regulation->scomp_id, $this->baseDirectory, $regulation], 'import.regulation.start');

        $totalChapters = 0;
        $totalControls = 0;

        // read chapters, e.g. regulations/nis2/1
        foreach ($this->foreachRegulationChapter() as $regulationChapterDirectory) {
            $regulationChapterSpl = new \SplFileInfo($regulationChapterDirectory);
            $regulationChapterExtracted = (new \Swark\Services\Data\Filesystem\Import\RegulationChapterExtractor($regulationChapterSpl))->extract();
            $regulationChapterExtracted[0]['regulation_id'] = $regulation->id;

            $chapter = Chapter::updateOrCreate(...$regulationChapterExtracted);
            yo_info("Chapter %s upserted...", [$chapter->external_id, $chapter], 'import.regulation.chapter.created');
            $totalControlsInChapter = 0;

            $this->compositeKeyContainer->set('regulation_chapter', $regulationScompId . ":" . $chapter->external_id, $chapter->id);

            // read controls, e.g. regulations/nis2/1/controls/1.md
            foreach ($this->foreachControl($regulationChapterSpl) as $controlFile) {
                $regulationControlSpl = new \SplFileInfo($controlFile);
                $regulationControl = (new \Swark\Services\Data\Filesystem\Import\RegulationControlExtractor($regulationControlSpl))->extract();
                $regulationControl[0]['regulation_id'] = $regulation->id;
                $regulationControl[0]['regulation_chapter_id'] = $chapter->id;

                $control = Control::updateOrCreate(...$regulationControl);
                yo_info("Control %s upserted", [$control->id, $control], 'import.regulation.control.created');

                $this->compositeKeyContainer->set('regulation_control', $regulationScompId . ":" . $control->external_id, $control->id);
                $totalControlsInChapter++;
                $totalControls++;
            }

            yo_info('Finished importing chapter with %d controls', [$totalControlsInChapter], 'import.regulation.chapter.finished');
            $totalChapters++;
        }

        yo_info('Total upserted: %d chapters, %d controls', [$totalChapters, $totalControls], 'import.regulation.finished');
    }

    /**
     * Iterate over controls in e.g. regulations/nis2/1/controls/1.md
     * @param \SplFileInfo $regulationChapterDirectory
     * @return \Generator|void
     */
    private function foreachControl(\SplFileInfo $regulationChapterDirectory)
    {
        $controlsDirectory = $regulationChapterDirectory->getRealPath() . '/' . static::CONTROLS_DIRECTORY;
        if (!file_exists($controlsDirectory)) {
            return;
        }

        foreach (new DirectoryIterator($controlsDirectory) as $controlFile) {
            if ($controlFile->isDot() || !$controlFile->isFile()) {
                continue;
            }

            yield $controlFile->getRealPath();
        }
    }

    /**
     * Iterate over chapters in e.g. regulations/nis2/1
     * @return \Generator
     */
    private function foreachRegulationChapter()
    {
        foreach (new DirectoryIterator($this->baseDirectory->getPathname()) as $regulationChapter) {
            if ($regulationChapter->isDot() || $regulationChapter->isFile()) {
                continue;
            }

            yield $regulationChapter->getRealPath();
        }
    }
}
