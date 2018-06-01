<?php
/**
 * Created by PhpStorm.
 * User: eugene
 * Date: 04/04/18
 * Time: 14:04
 */

require_once '../../../wrappers/entities/Address.php';

class AddressTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Address $address
     */
    private $address;

    /**
     * Create an address object
     */
    protected function setUp()
    {
        $this->address = new Address();
    }

    /**
     * Test an address converting to an an array
     */
    public function testAddressToArray()
    {
        $array = $this->address->toArray();
        $this->assertArrayHasKey('id', $array);
    }

    /**
     * Test filling an address with array
     */
    public function testInitWithValue()
    {
        $address = new Address([
            'postcode' => '07401',
            'country' => 'UA',
            'region' => 'Київська',
            'city' => 'Бровари',
            'district' => 'Київський',
            'street' => 'Котляревського',
            'houseNumber' => '12',
            'apartmentNumber' => '33']);

        $this->assertEquals('07401', $address->getPostcode());
    }
}
