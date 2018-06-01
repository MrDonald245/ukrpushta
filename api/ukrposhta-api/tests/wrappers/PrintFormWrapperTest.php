<?php
/**
 * Created by PhpStorm.
 * User: eugene
 * Date: 11/04/18
 * Time: 12:31
 */

require_once '../../wrappers/PrintFormWrapper.php';
require_once '../../kernel/UkrposhtaApi.php';
require_once '../../wrappers/UkrposhtaApiWrapper.php';

class PrintFormWrapperTest extends PHPUnit_Framework_TestCase
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

    public function testShipmentLabel()
    {
        $address1 = $this->createAddress();
        $address2 = $this->createAddress();

        $client1 = $this->createClient($address1->getId());
        $client2 = $this->createClient($address2->getId());

        $shipment = $this->createShipment($client1->getUuid(), $client2->getUuid());

        $result = $this->wrapper->printForm()->shipmentLabel($shipment->getUuid());
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
     * @param string $senderUuid
     * @param string $recipientUuid
     * @return Shipment
     */
    private function createShipment($senderUuid, $recipientUuid)
    {

        $shipment = new Shipment([
            'sender' => ['uuid' => $senderUuid],
            'recipient' => ['uuid' => $recipientUuid],
            'deliveryType' => 'W2D',
            'paidByRecipient' => true,
            'nonCashPayment' => false,
            'parcels' => [['weight' => 1200, 'length' => 170]],
        ]);

        $created_shipment = $this->wrapper->shipment()->create($shipment);

        return $created_shipment;
    }
}
