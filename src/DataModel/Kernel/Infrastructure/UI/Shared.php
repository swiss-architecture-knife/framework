<?php

namespace Swark\DataModel\Kernel\Infrastructure\UI;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Swark\DataModel\InformationTechnology\Domain\Entity\Cloud\AvailabilityZone;
use Swark\DataModel\InformationTechnology\Domain\Entity\Cloud\Region;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasDescription;

class Shared
{
    const ECOSYSTEM = 'Ecosystem';
    const SOFTWARE = 'Software';

    const COMPLIANCE = 'Compliance';
    const DEPLOYMENT = 'Deployment';
    const INFRASTRUCTURE = 'Infrastructure';

    const CLOUD = 'Cloud and Managed Services';

    const ENTERPRISE_ARCHITECTURE = 'Enterprise architecture';

    const ACTION = 'Actions';

    public static function scompId(): mixed
    {
        return TextInput::make('scomp_id')->unique(ignoreRecord: true);
    }

    public static function requiredName(bool $enableUnique = true): TextInput
    {
        $r = TextInput::make('name')->required();

        if ($enableUnique) {
            $r = $r->unique(ignoreRecord: true);
        }

        return $r;
    }

    public static function defaultGeneralSection(
        string  $description = 'Each element has a name',
        ?string $nameHint = null,
        ?string $clazz = null,
        bool    $withScompId = true, array $additionalSchema = []): mixed
    {
        $inputName = Shared::requiredName('name');

        if ($nameHint) {
            $inputName->hint($nameHint);
        }

        $schema = [
            $inputName,
        ];

        if ($withScompId) {
            $schema[] = Shared::scompId()->label('Scomp-ID')->hint('A Scomp-ID can be used for retrieving elements through a hierarchy');
        }

        if ($clazz && in_array(HasDescription::class, class_uses_recursive($clazz))) {
            $schema[] = Textarea::make('description')->nullable();
        }

        if (!empty($additionalSchema)) {
            $schema = array_merge($schema, $additionalSchema);
        }

        return
            Section::make('General')
                ->description($description)->schema($schema);
    }

    public static function selectServiceProvider(): mixed
    {
        return Select::make('managed_service_provider_id')
            ->relationship(
                name: 'managedServiceProvider',
                titleAttribute: 'name',
                modifyQueryUsing: fn(Builder $query) => $query->where('is_managed_service_provider', true)
            )->required();
    }

    /**
     * @param bool $required
     * @return mixed
     * @deprecated Use \Swark\Aspects\AssociatedWithOrganizations instead
     */
    public static function selectCustomer(bool $required = false): mixed
    {
        $r = Select::make('customer_id')
            ->relationship(
                name: 'customer',
                titleAttribute: 'name',
                modifyQueryUsing: fn(Builder $query) => $query->where('is_customer', true)
            );

        if ($required) {
            $r = $r->required();
        }

        return $r;
    }

    public static function selectVendor(bool $required = false): mixed
    {
        $r = Select::make('vendor_id')
            ->relationship(
                name: 'vendor',
                titleAttribute: 'name',
                modifyQueryUsing: fn(Builder $query) => $query->where('is_vendor', true)
            );

        if ($required) {
            $r = $r->required();
        }

        return $r;
    }


    public static function selectReleaseTrain(): Select
    {
        return Select::make('release_train_id')
            ->relationship('releaseTrain')
            ->required(false)
            ->getOptionLabelFromRecordUsing(function (Model $record) {
                $prefix = "";
                if ($record->system) {
                    $prefix = $record->system->name . ": ";
                }
                return $prefix . $record->name;
            });
    }

    public static function selectRegion(bool $required = false): mixed
    {
        $r = Select::make('region_id')
            ->relationship(
                name: 'region',
                titleAttribute: 'name',
            )
            ->getOptionLabelFromRecordUsing(fn(Region $record) => "{$record->managedServiceProvider->name}: {$record->name}");

        if ($required) {
            $r = $r->required();
        }

        return $r;
    }

    public static function selectAvailabilityZone(bool $required = false): mixed
    {
        $r = Select::make('availability_zone_id')
            ->relationship(
                name: 'availabilityZone',
                titleAttribute: 'name',
            )
            ->getOptionLabelFromRecordUsing(fn(AvailabilityZone $record) => "{$record->region->managedServiceProvider->name}: {$record->region->name} -> {$record->name}");

        if ($required) {
            $r = $r->required();
        }

        return $r;
    }

    public static function searchableSoftware(
        bool    $multiple = true,
        ?string $relationship = null,
    ): Select
    {
        $useRelationship = $multiple ? 'softwares' : 'software';

        if ($relationship) {
            $useRelationship = $relationship;
        }
        /*
                $searchResultCallback = fn(string $search): array => Software::whereHasMorph('softwareable', [Application::class], function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })->limit(50)->pluck('name', 'softwareable_id')->toArray();

                $optionLabelsCallback = fn(mixed $values): array => Software::whereHasMorph('softwareable', [Application::class], function ($query) use ($values) {
                    $query->whereIn('softwareable_id', $values);
                })->pluck('name', 'softwareable_id')->toArray();

                $optionLabelCallback = function (mixed $value): ?string {
                    $element = Software::whereHasMorph('softwareable', [Application::class], function ($query) use ($value) {
                        $query->whereIn('softwareable_id', is_array($value) ? $value : [$value]);
                    })->first();

                    if ($element) {
                        return $element->name;
                    }

                    return null;
                };
                */

        $r = Select::make('software')
            ->relationship($useRelationship, 'name')//            ->getSearchResultsUsing($searchResultCallback)
        ;

        if ($multiple) {
            $r = $r->multiple();
            // Searchable name is stored in parent class
//                ->getOptionLabelsUsing($optionLabelsCallback);
//        } else {
//            $r = $r->searchable()->getOptionLabelUsing($optionLabelCallback);
        }


        return $r;
    }
}
