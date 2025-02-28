<?php

namespace Swark\DataModel\Business\UI\ActorResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\View\View;
use Swark\DataModel\Business\UI\ActorResource;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasHelpSection;

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
