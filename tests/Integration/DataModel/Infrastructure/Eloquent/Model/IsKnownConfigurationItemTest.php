<?php

namespace Swark\Tests\Integration\DataModel\Infrastructure\Eloquent\Model;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Reader;
use PHPUnit\Framework\Attributes\Test;
use Swark\DataModel\Infrastructure\Eloquent\Model\Auditing\Action;
use Swark\DataModel\Infrastructure\Eloquent\Model\Auditing\Finding;
use Swark\DataModel\Infrastructure\Eloquent\Model\Auditing\Policy;
use Swark\DataModel\Infrastructure\Eloquent\Model\Auditing\Rule;
use Swark\DataModel\Infrastructure\Eloquent\Model\Auditing\Template;
use Swark\DataModel\Infrastructure\Eloquent\Model\Business\Actor;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\Chapter;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\Control;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\DataClassification;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\ProtectionGoal;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\Regulation;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Criticality;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Kpi\Metric;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Kpi\Period;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Strategy\Objective;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Strategy\Question;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Strategy\Strategy;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Technology;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud\Account;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud\Offer;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud\Subscription;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Baremetal;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Cluster;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Host;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Resource;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Runtime;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\System;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\ProtocolStack;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Zone;
use Swark\DataModel\Infrastructure\Eloquent\Model\Meta\ConfigurationItem;
use Swark\DataModel\Infrastructure\Eloquent\Model\Meta\Relationship;
use Swark\DataModel\Infrastructure\Eloquent\Model\Meta\ResourceType;
use Swark\DataModel\Infrastructure\Eloquent\Model\Operations\ApplicationInstance;
use Swark\DataModel\Infrastructure\Eloquent\Model\Operations\Stage;
use Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture\Software;
use Swark\DataModel\Infrastructure\Exchange\Excel\DataModelExcelFileFactory;
use Swark\DataModel\Infrastructure\Exchange\Excel\Import\DataModelExcelImport;
use Swark\DataModel\Infrastructure\Exchange\Excel\Import\DataModelImportOptions;
use Swark\DataModel\Infrastructure\Exchange\Excel\OrderedSheetExcelReader;
use Swark\DataModel\Infrastructure\Repository\Scope\ItemsByScompId;
use Swark\Tests\IntegrationTestCase;

class IsKnownConfigurationItemTest extends IntegrationTestCase
{
    use DatabaseTransactions;

    #[Test]
    public function forNewConfigurationItems_name_and_fullname_areGenerated()
    {
        // given
        $item = Actor::updateOrCreate(['name' => 'My actor']);

        // when
        $sut = ConfigurationItem::expectByInternalId(Actor::class, $item->id);

        // then
        // name and fullname are generated, also the scomp id
        $this->assertEquals('My actor', $sut->name);
        $this->assertEquals('My actor', $sut->fullname);
        $this->assertEquals('my_actor', $sut->scomp_id);
    }

    #[Test]
    public function nameAndFullNameOfConfigurationItems_canBeCustomized()
    {
        // given
        $item = CustomActor::updateOrCreate(['name' => 'My actor']);

        // when
        $sut = ConfigurationItem::expectByInternalId(Actor::class, $item->id);

        // then
        // custom name generation
        $this->assertEquals('some-short-name', $sut->name);
        $this->assertEquals('some-full-name', $sut->fullname);
        $this->assertEquals('my_actor', $sut->scomp_id);
    }

    #[Test]
    public function configurationItemsAreKeptInRegistry_ifModelIsDeleted(): void
    {
        // given
        $item = Actor::updateOrCreate(['name' => 'My actor']);
        $id = $item->id;
        $item->delete();

        // when
        $sut = ConfigurationItem::expectByInternalId(Actor::class, $item->id);
        $this->assertEquals('My actor', $sut->name);
        $this->assertNull(Actor::find($id));
    }

    #[Test]
    public function forNewConfigurationItems_whenItsScomIdHasAlreadyBeenRegistered_itsScompIdIsRandomized(): void
    {
        // given
        // an actor which is deleted
        $deletedItem = Actor::updateOrCreate(['name' => 'My actor']);
        $deletedItem->delete();
        $ciDeletedItem = ConfigurationItem::expectByInternalId(Actor::class, $deletedItem->id);

        // when
        // a new actor is created with the same name
        $newItem = Actor::updateOrCreate(['name' => 'My actor']);
        $ciNewItem = ConfigurationItem::expectByInternalId(Actor::class, $newItem->id);

        $this->assertNotEquals($ciNewItem->scomp_id, $ciDeletedItem->scomp_id);
        $this->assertStringStartsWith($ciDeletedItem->scomp_id . ':', $ciNewItem->scomp_id);
    }

    #[Test]
    public function onUpsertingAnExistingItem_theScompIdOfTheExistingItemIsStillUsed(): void {
        // given
        $criticality = Criticality::updateOrCreate(['name' => 'low']);
        $this->assertEquals('low', $criticality->configurationItem->scomp_id);

        // when
        $upsertedNew = Criticality::upsert('low', [
            'name' => 'new_name',
            'position' => '2',
        ]);

        // then
        // it is still the same because we are using the already existing scomp ID
        $this->assertEquals('low', $upsertedNew->configurationItem->scomp_id);
    }
}

class CustomActor extends Actor
{
    public function toConfigurationItemName(): ?string
    {
        return "some-short-name";
    }

    public function getMorphClass()
    {
        return 'actor';
    }

    public function toConfigurationItemFullName(): ?string
    {
        return "some-full-name";
    }
}
