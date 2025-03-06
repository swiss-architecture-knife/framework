<?php

namespace Swark\DataModel\Presenter\UI;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;
use Swark\Kernel\Infrastructure\Aspects\HasDescription;

class DataModelDecoratorFactory
{
    public function create(
        string $model,
        Form   $form): DataModelDecorator
    {
        return new DataModelDecorator($model, $form);
    }
}
