<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\IpNetworkResource\RelationManagers;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Swark\DataModel\InformationTechnology\Domain\Entity\Network\IpAddress;

class IpAddressAssignedRelationManager extends RelationManager
{
    protected static string $relationship = 'ipAddresses';

    protected static ?string $title = "IP addresses assigned";

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Network')->schema([
                    Select::make('ip_network_id')
                        ->relationship('ipNetwork', titleAttribute: 'network')
                        ->label('Subnet')
                        // depending upon the selected network, the IP address select list gets automatically pre-filled with IP addresses of that network
                        ->reactive(),
                ]),
                Section::make('IP address')->schema([
                    Select::make('ip_address_id')
                        ->label('Select existing IP address in network')
                        ->placeholder('Select IP address OR create a new one')
                        // listen to "Select Subnet" changes and update select list entries accordingly
                        ->options(fn(Get $get): \Illuminate\Support\Collection => IpAddress::query()
                            ->where('ip_network_id', $get('ip_network_id'))
                            ->pluck('address', 'id'))
                        // if no network is select, disable this select box
                        ->disabled(fn(Get $get) => empty($get('ip_network_id')))
                        // also make it reactive so that the manual 'address' input can listen to it
                        ->reactive(),
                    TextInput::make('address')
                        ->label('OR create new address')
                        // disable this input box *if* an already existing IP address is selected
                        ->disabled(fn(Get $get) => !empty($get('ip_address_id')))
                        ->ip()
                ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('address')
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['ipNetwork']))
            ->columns([
                TextColumn::make('ipNetwork.network')->label('Network'),
                TextColumn::make('address'),
                TextColumn::make('description'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Assign IP address')
                    /**
                     * When storing the data inside the database, we
                     * <ul><li>either want to create a new IP address</li>
                     * <li>or -if an existing IP address has been selected- we only want to create the association to that IP address.</li>
                     * </ul>
                     */
                    ->using(function (array $data, Table $table): Model {
                        $model = IpAddress::class;
                        $relationship = $table->getRelationship();

                        $pivotData = [];

                        if ($relationship instanceof BelongsToMany) {
                            // ip_address_assigned table
                            $pivotColumns = $relationship->getPivotColumns();

                            $pivotData = Arr::only($data, $pivotColumns);
                            $data = Arr::except($data, $pivotColumns);
                        }

                        // prevent duplicate IP addresses in the same network.
                        // That may happen if a user selects a network or leaves it empty and enters an already existing IP address in that network scope.
                        if (!empty($data['address'])) {
                            $query = IpAddress::withIp($data['address']);

                            if (!empty($data['ip_network_id'])) {
                                $query = $query->where('ip_network_id', $data['ip_network_id']);
                            }

                            // that IP does already exist, so use this IP address' ID.
                            if ($alreadyExistingIP = $query->first()) {
                                $data['ip_address_id'] = $alreadyExistingIP->id;
                            }
                        }

                        if (!empty($data['ip_address_id'])) {
                            $record = IPAddress::where('id', $data['ip_address_id'])->firstOrFail();
                        } else {
                            $record = new $model;
                            $record->fill($data);
                        }

                        // save morph-to relationship
                        $relationship->save($record, $pivotData);

                        return $record;
                    })
            ])
            ->actions([
                DetachAction::make()
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
