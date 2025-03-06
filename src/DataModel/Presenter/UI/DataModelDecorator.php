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

class DataModelDecorator
{
    public function __construct(
        public readonly string $model,
        public readonly Form   $form)
    {
    }

    private ?string $nameHint = null;

    public function nameHint(?string $name): DataModelDecorator
    {
        $this->nameHint = $name;
        return $this;
    }

    protected function appendName(array $section): array
    {
        if ($this->hasTrait(HasName::class)) {
            $inputName = Shared::requiredName('name');

            if ($this->nameHint) {
                $inputName = $inputName->hint($this->nameHint);
            }

            $section[] = $inputName;
        }

        return $section;
    }

    protected function appendDescription(array $section): array
    {
        if ($this->hasTrait(HasDescription::class)) {
            $section[] = Textarea::make('description')->nullable();
        }

        return $section;
    }

    private function hasTrait(string $clazzOrTrait): bool
    {
        return in_array($clazzOrTrait, class_uses_recursive($this->model));
    }

    private function inheritsFrom(string $clazz): bool
    {
        return in_array($clazz, class_parents($this->model));
    }

    private ?string $description = null;

    public function generalDescription(string $description): DataModelDecorator
    {
        $this->description = $description;
        return $this;
    }

    private array $generalSectionAdditionalSchema = [];

    public function generalSection(array $additionalSchema = []): DataModelDecorator
    {
        $this->generalSectionAdditionalSchema = $additionalSchema;
        return $this;
    }

    private array $additionalSectionsSchema = [];

    public function sections(array $additionalSchema = []): DataModelDecorator
    {
        $this->additionalSectionsSchema = $additionalSchema;
        return $this;
    }

    protected function createGeneralSection(): ?Section
    {
        $section = null;
        $schema = [];

        $schema = $this->appendName($schema);
        $schema = $this->appendDescription($schema);

        $schema = array_merge($schema, $this->generalSectionAdditionalSchema);

        if (!empty($schema)) {
                $section = Section::make('General')
                    ->description($this->description)
                    ->schema($schema);
            }

        return $section;
    }

    protected function createMetaSection(): ?Section
    {
        if (!$this->inheritsFrom(IsKnownConfigurationItem::class)) {
            return null;
        }

        return Section::make('Meta')->relationship('configurationItem')->schema([
            TextInput::make('scomp_id')
                ->readOnly(true)
                ->label('Scomp ID')
                ->hint('Provide a unique name for this configuration item'),
            TextInput::make('name')
                ->readOnly(true)->label('Global name'),
            TextInput::make('name')
                ->readOnly(true)->label('Global full name'),
            DateTimePicker::make('created_at')->readonly(true)->label('Created at'),
            DateTimePicker::make('updated_at')->readonly(true)->label('Updated at'),
            DateTimePicker::make('last_seen_at')->readonly(true)->label('Last seen'),
        ])->collapsed(true);
    }

    public function decorate(): Form
    {
        $schema = [];

        if ($generalSection = $this->createGeneralSection()) {
            $schema[] = $generalSection;
        }

        $schema = array_merge($schema, $this->additionalSectionsSchema);

        if ($metaSection = $this->createMetaSection()) {
            $schema[] = $metaSection;
        }

        return $this->form->schema($schema);
    }

    public static function of(string $model, Form $form)
    {
        $schema = [];;


        return new static($model, $form);
    }
}
