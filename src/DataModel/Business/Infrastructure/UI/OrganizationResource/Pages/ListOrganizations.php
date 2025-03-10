<?php

namespace Swark\DataModel\Business\Infrastructure\UI\OrganizationResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\View\View;
use Swark\DataModel\Business\Infrastructure\UI\OrganizationResource;

class ListOrganizations extends ListRecords
{
   // use HasHelpSection;

    protected static string $resource = OrganizationResource::class;

    protected ?string $subheading = 'Configure vendors, customers and managed service providers of your supply chain';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public static function getHelpSection(): View
    {
        return view('help.actors');
    }
}
