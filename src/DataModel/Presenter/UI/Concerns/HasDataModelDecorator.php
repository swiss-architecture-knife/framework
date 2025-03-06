<?php

namespace Swark\DataModel\Presenter\UI\Concerns;

use Filament\Forms\Form;
use Swark\DataModel\Presenter\UI\DataModelDecoratorFactory;

trait HasDataModelDecorator {
    public static function decorator(Form $form, ?string $model = null) {
        $model = $model ?? static::$model;
        throw_if(!$model, "A model class must be passed to create a new DataModelDecorator");

        return app()->make(DataModelDecoratorFactory::class)->create($model, $form);
    }
}
