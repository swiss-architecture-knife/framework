<?php

namespace Swark\DataModel\Presenter\UI\Business\ActorResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\View\View;
use Swark\DataModel\Infrastructure\Aspects\HasHelpSection;
use Swark\DataModel\Presenter\UI\Business\ActorResource;

class ListActors extends ListRecords
{
    use HasHelpSection;

    protected static string $resource = ActorResource::class;

    protected ?string $subheading = 'Configure roles, persons and groups interacting with your landscape';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public static function getHelpSection(): View
    {
        return swark_view('help.actors');
    }
}
