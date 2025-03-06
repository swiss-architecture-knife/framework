<?php

namespace Swark\DataModel\Presenter\UI;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Panel;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;
use Swark\Kernel\Infrastructure\Aspects\HasDescription;

class PanelDecoratorFactory
{
    public function create(
        Panel $panel): PanelDecorator
    {
        return new PanelDecorator($panel);
    }
}
