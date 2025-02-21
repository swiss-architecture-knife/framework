<?php

namespace Swark\Management\Architecture\Resources\Ecosystem\ActorResource\RelationManagers;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

abstract class C4ArchitectureRelationManager extends RelationManager
{
    protected static string $relationship = 'from';
    protected static ?string $inverseRelationship = 'to';
    protected static ?string $title = 'Incoming relationships';
    protected static ?string $otherEndLabel = 'From';

    protected static string $recordSelectSearchColumns = 'name';

    protected static Direction $direction = Direction::OUTGOING;

    protected function recordSelectSearchColumns(): array
    {
        return [static::$recordSelectSearchColumns];
    }

    protected function otherEndLabel(): string
    {
        return static::$otherEndLabel;
    }

    public static function createRelations(array $incoming = [], array $outgoing = []): array
    {
        $r = [];

        if (sizeof($incoming) > 0) {
            $r[] = RelationGroup::make('Incoming relationships', $incoming)
                ->badge('from →')
                ->badgeColor('danger');
        }

        if (sizeof($outgoing) > 0) {
            $r[] = RelationGroup::make('Outgoing relationships', $outgoing
            )->badge('→ to')
                ->badgeColor('danger');
        }

        return $r;
    }

    protected function createAttachForm(Form $form, Tables\Actions\AttachAction $action): array
    {
        $components = match (static::$direction) {
            Direction::INCOMING => [
                Section::make($this->otherEndLabel())->schema([
                    $action->getRecordSelect()
                        ->label($this->otherEndLabel())->hint($this->otherEndLabel())
                        ->required(),
                    TextInput::make('source_name')->label("Role name of that side of the relationships"),
                ]),
                TextInput::make('target_name')->label("This side's relation name"),
            ],
            Direction::OUTGOING => [
                TextInput::make('source_name')->label("This side's relation name"),
                Section::make($this->otherEndLabel())->schema([
                    $action->getRecordSelect()
                        ->label($this->otherEndLabel())->hint($this->otherEndLabel())
                        ->required(),
                    TextInput::make('target_name')->label("Role name of that side of the relationships"),
                ]),

            ]
        };

        $components[] = Section::make('connection')->schema([
            Select::make('direction')->options(['unidirectional' => 'Unidirectional', 'bidirectional' => 'Bidirectional'])->required(),
            Textarea::make('description'),
        ]);

        return $components;
    }

    protected function createAttachAction(): Tables\Actions\AttachAction
    {
        return Tables\Actions\AttachAction::make()
            //->preloadRecordSelect()
            ->recordSelectSearchColumns($this->recordSelectSearchColumns())
            ->form(function (Form $form, Tables\Actions\AttachAction $action) {
                return $this->createAttachForm($form, $action);
            });
    }

    protected function nameTextColumn(): Tables\Columns\TextColumn
    {
        return Tables\Columns\TextColumn::make('name')->label($this->otherEndLabel());

    }

    public function table(Table $table): Table
    {
        $columns = match (static::$direction) {
            Direction::INCOMING => [
                $this->nameTextColumn(),
                Tables\Columns\TextColumn::make('source_name')->label('From role'),
                Tables\Columns\TextColumn::make('target_name')->label("This side's role")
            ],
            Direction::OUTGOING => [
                Tables\Columns\TextColumn::make('source_name')->label("This side's role"),
                $this->nameTextColumn(),
                Tables\Columns\TextColumn::make('target_name')->label("Other side's role"),
            ]
        };

        $columns[] = Tables\Columns\TextColumn::make('description')->label('Description');

        return $table
            ->recordTitleAttribute('name')
            ->columns(
                $columns)
            ->filters([
                //
            ])
            ->headerActions([
                $this->createAttachAction()
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
