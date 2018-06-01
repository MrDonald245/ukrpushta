<?php
/**
 * Created by PhpStorm.
 * User: eugene
 * Date: 10/04/18
 * Time: 15:18
 */

require_once '../../wrappers/ShipmentGroupWrapper.php';
require_once '../../wrappers/entities/ShipmentGroup.php';
require_once '../../kernel/UkrposhtaApi.php';
require_once '../../wrappers/UkrposhtaApiWrapper.php';

class ShipmentGroupWrapperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var UkrposhtaApiWrapper
     */
    private $wrapper;

    public function setUp()
    {
        $this->wrapper = new UkrposhtaApiWrapper(
            'f9027fbb-cf33-3e11-84bb-5484491e2c94',
            'ba5378df-985e-49c5-9cf3-d222fa60aa68');
    }

    /**
     * @return ShipmentGroup
     */
    public function testCreate()
    {
        $address = $this->createAddress();
        $client = $this->createClient($address->getId());

        $shipment_group = $this->wrapper->shipmentGroup()->create(new ShipmentGroup([
            'name' => 'Group 1',
            'clientUuid' => $client->getUuid(),
        ]));

        $this->assertEquals('Group 1', $shipment_group->getName());

        return $shipment_group;
    }


    /**
     * @depends testCreate
     * @param ShipmentGroup $shipmentGroup
     */
    public function testEdit($shipmentGroup)
    {
        $edited_shipment_group = $this->wrapper->shipmentGroup()
            ->edit($shipmentGroup->getUuid(), ['name' => 'new name']);
        $this->assertEquals('new name', $edited_shipment_group->getName());
    }

    /**
     * @depends testCreate
     * @param ShipmentGroup $shipmentGroup
     * @return Shipment
     */
    public function testAddShipment($shipmentGroup)
    {
        $shipment = $this->createShipment();
        $result = $this->wrapper->shipmentGroup()
            ->addShipment($shipment->getUuid(), $shipmentGroup->getUuid());
        $this->assertArrayHasKey('message', $result);

        return $this->wrapper->shipment()->getByUuid($shipment->getUuid());
    }

    /**
     * @depends testCreate
     * @param ShipmentGroup $shipmentGroup
     */
    public function testGet($shipmentGroup)
    {
        $fetched_shipment_group = $this->wrapper->shipmentGroup()->get($shipmentGroup->getUuid());
        $this->assertEquals($shipmentGroup->getUuid(), $fetched_shipment_group->getUuid());
    }

    /**
     * @depends testCreate
     * @param ShipmentGroup $shipmentGroup
     */
    public function testGetByClientUuid($shipmentGroup)
    {
        $first_fetched_shipment_group = $this->wrapper->shipmentGroup()
            ->getByClientUuid($shipmentGroup->getClientUuid())[0];

        $this->assertEquals(true,$first_fetched_shipment_group->getUuid() != null);
    }

    /**
     * @depends testAddShipment
     * @param Shipment $shipment
     */
    public function testDeleteShipment($shipment)
    {
        $this->wrapper->shipmentGroup()->deleteShipment($shipment->getUuid());
        $fetched_shipment = $this->wrapper->shipment()->getByUuid($shipment->getUuid());

        $this->assertEquals(true, $fetched_shipment->getShipmentGroupUuid() == null);
    }

    /**
     * @return Address
     */
    private function createAddress()
    {
        return $this->wrapper->address()->create(['postcode' => '07401',
            'country' => 'UA',
            'region' => 'Київська',
            'city' => 'Бровари',
            'district' => 'Київський',
            'street' => 'Котляревського',
            'houseNumber' => '12',
            'apartmentNumber' => '33']);
    }

    /**
     * @param int $addressId
     * @return Client
     */
    private function createClient($addressId)
    {
        return $this->wrapper->client()->create([
            'name' => 'ТОВ Експресс Банк',
            'uniqueRegistrationNumber' => '0035',
            'addressId' => $addressId,
            'phoneNumber' => '067 123 12 34',
            'resident' => true,
            'edrpou' => '20053145',
            'email' => 'test@test.com',]);
    }

    /**
     * @return Shipment
     */
    private function createShipment()
    {
        $sender_address = $this->createAddress();
        $recipient_address = $this->createAddress();

        $sender = $this->createClient($sender_address->getId());
        $recipient = $this->createClient($recipient_address->getId());

        $shipment = new Shipment([
            'sender' => ['uuid' => $sender->getUuid()],
            'recipient' => ['uuid' => $recipient->getUuid()],
            'deliveryType' => 'W2D',
            'paidByRecipient' => true,
            'nonCashPayment' => false,
            'parcels' => [['weight' => 1200, 'length' => 170]],
        ]);

        $created_shipment = $this->wrapper->shipment()->create($shipment);

        return $created_shipment;
    }
}
