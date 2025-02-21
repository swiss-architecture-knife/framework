<?php

namespace Swark\DataModel\Infrastructure\Domain\Model;

use Illuminate\Database\Eloquent\Model;

class ExecutionEnvironment
{
    protected function __construct(public readonly Model         $record,
                                   public readonly ?ExecutorType $executorType,
                                   public readonly ?int          $executorId,
                                   public readonly ?string       $executorName,
                                   public readonly ?int          $softwareId,
                                   public readonly ?string       $softwareName,
                                   public readonly ?int          $releaseId,
                                   public readonly ?string       $releaseName,
                                   public readonly ?string       $stageName,
    )
    {
    }

    public function executor(): string
    {
        return ($this->executorType ?? ExecutorType::UNKNOWN)->value . ":" . $this->executorName;
    }

    public function release(): string
    {
        return $this->softwareName . ":" . $this->releaseName;
    }

    public function title(): string
    {
        return $this->executor() . ":" . $this->release() . ":" . ($this->stageName ?? '?');
    }

    public static function from(Model $record): ExecutionEnvironment
    {
        $executorType = ExecutorType::UNKNOWN;
        $executorId = null;
        $executorName = null;
        $softwareId = null;
        $softwareName = null;
        $releaseId = null;
        $releaseName = null;
        $stageName = null;

        $executorType = ExecutorType::from((string)$record->executor_type);
        $executorId = $record->executor_id;

        // this is an Eloquent query for ApplicationInstance
        if ($record->executor) {
            $executorName = $record->executor->name;
            $softwareName = $record->release->software->name;
            $softwareId = $record->release->software->id;
            $releaseName = $record->release->version;
            $releaseId = $record->release->id;
            $stageName = $record->stage?->name;
        } // this is an Eloquent query for a search result across different table columns
        else {
            $executorName = match ($executorType) {
                ExecutorType::RUNTIME => $record->runtime_name,
                ExecutorType::HOST => $record->runtime_name,
                ExecutorType::DEPLOYMENT => $record->runtime_name,
            };
            $softwareName = $record->software_name;
            $softwareId = $record->software_id;
            $releaseId = $record->release_id;
            $releaseName = $record->release_name;
            $stageName = $record->stage_name;
        }

        return new static($record, $executorType, $executorId, $executorName, $softwareId, $softwareName, $releaseId, $releaseName, $stageName);
    }
}
