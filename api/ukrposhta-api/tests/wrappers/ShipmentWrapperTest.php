<?php
/**
 * Created by Eugene.
 * User: eugene
 * Date: 10/04/18
 * Time: 12:00
 */

require_once '../../wrappers/ShipmentWrapper.php';
require_once '../../wrappers/entities/Shipment.php';
require_once '../../kernel/UkrposhtaApi.php';
require_once '../../wrappers/UkrposhtaApiWrapper.php';

class ShipmentWrapperTest extends PHPUnit_Framework_TestCase
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

    public function testCreate()
    {
        $address = $this->createAddress();
        $sender = $this->createClient($address->getId());
        $recipient = $this->createClient($address->getId());

        $shipment = new Shipment([
            'sender' => ['uuid' => $sender->getUuid()],
            'recipient' => ['uuid' => $recipient->getUuid()],
            'deliveryType' => Shipment::DELIVERY_TYPE_W2D,
            'paidByRecipient' => true,
            'nonCashPayment' => false,
            'parcels' => [['weight' => 1200, 'length' => 170]],
        ]);

        $created_shipment = $this->wrapper->shipment()->create($shipment);
        $this->assertEquals(true, $created_shipment->getUuid() != null);

        return $created_shipment;
    }

    /**
     * @depends testCreate
     * @param Shipment $shipment
     */
    public function testEdit($shipment)
    {
        $edited_shipment = $this->wrapper->shipment()->edit($shipment->getUuid(), ['description' => 'new description']);
        $this->assertEquals('new description', $edited_shipment->getDescription());
    }

    /**
     * @depends testCreate
     * @param Shipment $shipment
     */
    public function testGetByUuid($shipment)
    {
        $fetched_shipment = $this->wrapper->shipment()->getByUuid($shipment->getUuid());
        $this->assertEquals($shipment->getUuid(), $fetched_shipment->getUuid());
    }

    /**
     * @depends testCreate
     * @param Shipment $shipment
     */
    public function testDelete($shipment)
    {
        $this->wrapper->shipment()->delete($shipment->getUuid());
        $this->expectException(UkrposhtaApiException::class);
        $this->wrapper->shipment()->getByUuid($shipment->getUuid());
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
}
