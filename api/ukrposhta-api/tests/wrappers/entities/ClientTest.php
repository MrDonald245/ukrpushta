<?php
/**
 * Created by Eugene.
 * User: eugene
 * Date: 05/04/18
 * Time: 16:00
 */

require_once '../../../wrappers/entities/Client.php';

class ClientTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test filling an address with array
     */
    public function testInitWithValue()
    {
        $client = new Client([
            'name' => 'ТОВ Експресс Банк',
            'uniqueRegistrationNumber' => '0035',
            'addressId' => 56922,
            'phoneNumber' => '067 123 12 34',
            'bankCode' => '123000',
            'bankAccount' => '111000222000999',
            'resident' => true,
            'edrpou' => '20053145',
            'email' => 'test@test.com',
        ]);

        $this->assertEquals(56922, $client->getAddressId());

        return $client;
    }

    /**
     * Test an address converting to an an array
     *
     * @depends testInitWithValue
     * @param Address $client
     */
    public function testClientToArray($client)
    {
        $array = $client->toArray();
        $this->assertArrayHasKey('uuid', $array);
    }

}
