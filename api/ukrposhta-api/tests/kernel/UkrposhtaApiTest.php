<?php
/**
 * Created by Eugene.
 * User: eugene
 * Date: 30/03/18
 * Time: 09:27
 */

require_once '../../kernel/UkrposhtaApi.php';
require_once 'UkrPoshtaTestExpectedKeys.php';

class UkrposhtaApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var UkrposhtaApi $api
     */
    private $api;

    /**
     * Create UkrposhtaApi instance
     */
    protected function setUp()
    {
        $this->api = new UkrposhtaApi(
            'f9027fbb-cf33-3e11-84bb-5484491e2c94',
            'ba5378df-985e-49c5-9cf3-d222fa60aa68');
    }

    /**
     * Test address creation
     *
     * @return array
     */
    public function testCreateAddress()
    {
        $address = $this->createAddressesWithApi($this->api);
        $this->checkRequestArrayKeys($address, UkrPoshtaTestExpectedKeys::ADDRESS_VALID_KEYS);

        return $address;
    }

    /**
     * Get the address by id of already created one
     *
     * @depends testCreateAddress
     * @param $address array
     * @return array $address
     */
    public function testGetAddress($address)
    {
        $address = $this->api->method('GET')->addresses($address['id']);
        $this->checkRequestArrayKeys($address, UkrPoshtaTestExpectedKeys::ADDRESS_VALID_KEYS);

        return $address;
    }

    /**
     * Create a client.
     *
     * @depends testGetAddress
     * @param $address array
     * @return array $client
     */
    public function testCreateClient($address)
    {
        $client = $this->createClientWithApi($this->api, $address);
        $this->checkRequestArrayKeys($client, UkrPoshtaTestExpectedKeys::CLIENT_VALID_KEYS);

        return $client;
    }

    /**
     * Get a client
     *
     * @depends testCreateClient
     * @param array $client
     * @return array
     */
    public function testGetClient($client)
    {
        $client = $this->api->method('GET')->action('getById')->clients($client['uuid']);
        $this->checkRequestArrayKeys($client, UkrPoshtaTestExpectedKeys::CLIENT_VALID_KEYS);

        return $client;
    }

    /**
     * @depends testCreateClient
     * @param array $client
     */
    public function testGetClientByPhone($client)
    {
        $client = $this->api->method('GET')->action('getByPhone')->clients($client['phoneNumber'])[0];
        $this->checkRequestArrayKeys($client, UkrPoshtaTestExpectedKeys::CLIENT_VALID_KEYS);
    }

    /**
     * @depends testCreateClient
     * @param array $client
     */
    public function testGetAllClientsPhones($client)
    {
        $phones = $this->api->method('GET')->action('getAllPhones')->clients($client['uuid'])[0];

        $this->assertArrayHasKey('uuid', $phones);
        $this->assertArrayHasKey('phoneNumber', $phones);
        $this->assertArrayHasKey('type', $phones);
        $this->assertArrayHasKey('main', $phones);

    }

    /**
     * @depends testCreateClient
     * @param array $client
     */
    public function testGetAllClientsAddress($client)
    {
        $addresses = $this->api->method('GET')->action('getAllAddresses')->clients($client['uuid'])[0];

        $this->assertArrayHasKey('uuid', $addresses);
        $this->assertArrayHasKey('addressId', $addresses);
        $this->assertArrayHasKey('type', $addresses);
        $this->assertArrayHasKey('main', $addresses);
    }

    /**
     * @depends testCreateClient
     * @param array $client
     */
    public function testGetAllClientsEmails($client)
    {
        $emails = $this->api->method('GET')->action('getAllEmails')->clients($client['uuid'])[0];

        $this->assertArrayHasKey('uuid', $emails);
        $this->assertArrayHasKey('email', $emails);
        $this->assertArrayHasKey('main', $emails);
    }

    /**
     * @depends testCreateClient
     * @param array $client
     * @return array $client
     */
    public function testAddAddress($client)
    {
        $new_address = $this->createAddressesWithApi($this->api);
        $client_result = $this->api->method('PUT')
            ->params(['addressId' => $new_address['id']])
            ->clients($client['uuid']);

        $address_count = sizeof($client_result['addresses']);
        $this->assertEquals(2, $address_count);

        return $client_result;
    }

    /**
     * @depends testCreateClient
     * @param array $client
     * @return array $client
     */
    public function testAddPhone($client)
    {
        $client_result = $this->api->method('PUT')
            ->params(['phoneNumber' => '+3809999999'])
            ->clients($client['uuid']);

        return $client_result;
    }

    /**
     * @depends testAddPhone
     * @param array $client
     */
    public function testDeleteClientsPhone($client)
    {
        $phone_uuid = '';
        $old_phones_size = sizeof($client['phones']);
        foreach ($client['phones'] as $phone) {
            if ($phone['main'] == false) {
                $phone_uuid = $phone['uuid'];
            }
        }

        $this->api->method('DELETE')->action('deletePhone')->clients($phone_uuid);

        $new_phones_size = sizeof($this->api->method('GET')
            ->action('getAllPhones')->clients($client['uuid']));

        $this->assertEquals(false, $new_phones_size == $old_phones_size);
    }

    /**
     * @depends testAddAddress
     * @param array $client
     */
    public function testDeleteClientsAddress($client)
    {
        $address_uuid = '';
        $old_addresses_size = sizeof($client['addresses']);
        foreach ($client['addresses'] as $address) {
            if ($address['main'] == false) {
                $address_uuid = $address['uuid'];
            }
        }

        $this->api->method('DELETE')->action('deleteAddress')->clients($address_uuid);

        $new_addresses_size = sizeof($this->api->method('GET')
            ->action('getAllAddresses')->clients($client['uuid']));

        $this->assertEquals(false, $new_addresses_size == $old_addresses_size);
    }

    /**
     * Test PUT method for client
     *
     * @depends testGetClient
     * @param $client
     * @return array $client
     */
    public function testChangeClient($client)
    {
        $client = $this->api->method('PUT')
            ->params(['firstName' => 'Златан'])
            ->clients($client['uuid']);
        $this->assertEquals('Златан', $client['firstName']);

        return $client;
    }

    /**
     * Test creation of a shipment
     */
    public function testCreateShipment()
    {
        $shipment = $this->createShipmentWithApi($this->api);
        $this->checkRequestArrayKeys($shipment, UkrPoshtaTestExpectedKeys::SHIPMENT_VALID_KEYS);

        return $shipment;
    }

    /**
     * Test of getting a shipment
     *
     * @depends testCreateShipment
     * @param array $shipment
     * @return array $shipment
     */
    public function testGetShipment($shipment)
    {
        $shipment = $this->api->method('GET')->shipments($shipment['uuid']);
        $this->checkRequestArrayKeys($shipment, UkrPoshtaTestExpectedKeys::SHIPMENT_VALID_KEYS);

        return $shipment;
    }

    /**
     * Test PUT method for shipment
     *
     * @depends testGetShipment
     * @param $shipment
     * @return array $shipment
     */
    public function testChangeShipment($shipment)
    {
        $shipment = $this->api->method('PUT')->params(['paidByRecipient' => 'false'])->shipments($shipment['uuid']);
        $this->assertEquals(false, $shipment['paidByRecipient']);

        return $shipment;
    }

    /**
     * Test of removing a shipment
     * @depends testChangeShipment
     * @param $shipment
     */
    public function testDeleteShipment($shipment)
    {
        $this->expectException(UkrposhtaApiException::class);

        $this->api->method('DELETE')->shipments($shipment['uuid']);
        $this->api->method('GET')->shipments($shipment['uuid']);
    }

    /**
     * Test creation of a client
     *
     * @return array $shipment_group
     */
    public function testCreateShipmentsGroup()
    {
        $shipment_group = $this->createShipmentGroupWithApi($this->api);
        $this->checkRequestArrayKeys($shipment_group, UkrPoshtaTestExpectedKeys::SHIPMENT_GROUP_VALID_KEYS);

        return $shipment_group;
    }


    public function testAddingShipmentToShipmentGroup()
    {
        $shipment = $this->createShipmentWithApi($this->api);
        $shipment_group = $this->createShipmentGroupWithApi($this->api);

        $this->api->method('POST')
            ->action('addShipment')
            ->shipmentGroups($shipment_group['uuid'], $shipment['uuid']);

        return $shipment_group;
    }

    /**
     * Test of getting a shipment group
     *
     * @depends testCreateShipmentsGroup
     * @param array $shipment_group
     * @return array
     */
    public function testGetShipmentGroup($shipment_group)
    {
        $shipment_group = $this->api->method('GET')->action('get')->shipmentGroups($shipment_group['uuid']);
        $this->checkRequestArrayKeys($shipment_group, UkrPoshtaTestExpectedKeys::SHIPMENT_GROUP_VALID_KEYS);

        return $shipment_group;
    }

    /**
     * Test of changing a shipment group
     *
     * @depends testGetShipmentGroup
     * @param array $shipment_group
     * @return array $shipment_group
     */
    public function testChangeShipmentGroup($shipment_group)
    {
        $shipment_group = $this->api->method('PUT')
            ->params(['name' => 'Eugene'])->shipmentGroups($shipment_group['uuid']);

        $this->assertEquals('Eugene', $shipment_group['name']);

        return $shipment_group;
    }

    /**
     * Test of deleting a shipment group
     *
     */
    public function testDeleteShipmentFromShipmentGroup()
    {
        $shipment_group = $this->createShipmentGroupWithApi($this->api);
        $shipment = $this->createShipmentWithApi($this->api);
        $this->api->method('POST')
            ->action('addShipment')
            ->shipmentGroups($shipment_group['uuid'], $shipment['uuid']);

        $this->api->method('DELETE')->shipmentGroups($shipment['uuid']);
    }

    /**
     * Check an exception if wrong bearer is used
     */
    public function testWrongBearer()
    {
        $this->expectException(UkrposhtaApiException::class);

        $api = new UkrposhtaApi(
            'silly bearer',
            'ba5378df-985e-49c5-9cf3-d222fa60aa68');

        $this->createAddressesWithApi($api);
    }

    /**
     * @depends testGetAddress
     * @param array $address
     */
    public function testCreateClientWithWrongToken($address)
    {
        $this->expectException(UkrposhtaApiException::class);

        $api = new UkrposhtaApi(
            'f9027fbb-cf33-3e11-84bb-5484491e2c94',
            'silly token');

        $this->createClientWithApi($api, $address);
    }

    /**
     * Checks all required address' keys.
     *
     * @param array $array
     * @param array $valid_keys
     */
    private function checkRequestArrayKeys($array, $valid_keys)
    {
        foreach ($valid_keys as $valid_key) {
            $this->assertArrayHasKey($valid_key, $array);
        }
    }

    /**
     * Create address
     *
     * @param UkrposhtaApi $api
     * @return array $addresses
     */
    private function createAddressesWithApi($api)
    {
        return $api->method('POST')->params([
            'postcode' => '07401',
            'country' => 'UA',
            'region' => 'Київська',
            'city' => 'Бровари',
            'district' => 'Київський',
            'street' => 'Котляревського',
            'houseNumber' => '12',
            'apartmentNumber' => '33'
        ])->addresses();
    }

    /**
     * Create client
     *
     * @param UkrposhtaApi $api
     * @param array $address
     * @return array $addresses
     */
    private function createClientWithApi($api, $address)
    {
        return $api->method('POST')->params([
            'firstName' => 'Евгений',
            'middleName' => 'Константинович',
            'lastName' => 'Бочарников',
            'individual' => true,
            'uniqueRegistrationNumber' => '0035',
            'addressId' => $address['id'],
            'phoneNumber' => '039052406',
            'resident' => true,
            'email' => 'test@test.com',
        ])->clients();
    }

    /**
     * @param UkrposhtaApi $api
     * @return array $shipment_group
     */
    private function createShipmentGroupWithApi($api)
    {
        $address = $this->createAddressesWithApi($api);
        $client = $this->createClientWithApi($api, $address);

        $shipment_group = $this->api->method('POST')->action('create')->params([
            'name' => 'Group 1',
            'clientUuid' => $client['uuid']
        ])->shipmentGroups();

        return $shipment_group;
    }

    /**
     * @param UkrposhtaApi $api
     * @return array $shipment
     */
    private function createShipmentWithApi($api)
    {
        $sender_address = $this->createAddressesWithApi($api);
        $recipient_address = $this->createAddressesWithApi($api);

        $sender_uuid = $this->createClientWithApi($api, $sender_address)['uuid'];
        $recipient_uuid = $this->createClientWithApi($api, $recipient_address)['uuid'];

        $shipment = $api->method('POST')->params([
            'sender' => ['uuid' => $sender_uuid],
            'recipient' => ['uuid' => $recipient_uuid],
            'deliveryType' => 'W2D',
            'paidByRecipient' => 'true',
            'nonCashPayment' => 'false',
            'parcels' => [['weight' => 1200, 'length' => 170]]
        ])->shipments();

        return $shipment;
    }
}