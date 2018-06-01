<?php
/**
 * Created by Eugene.
 * User: eugene
 * Date: 05/04/18
 * Time: 10:48
 */

require_once '../../wrappers/AddressWrapper.php';
require_once '../../kernel/UkrposhtaApi.php';

class AddressWrapperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var AddressWrapper
     */
    private $wrapper;

    public function setUp()
    {
        $this->wrapper = new AddressWrapper(
            'f9027fbb-cf33-3e11-84bb-5484491e2c94',
            'ba5378df-985e-49c5-9cf3-d222fa60aa68');
    }

    public function testCreateAddressWithArray()
    {
        $address_data = ['postcode' => '07401',
            'country' => 'UA',
            'region' => 'Київська',
            'city' => 'Бровари',
            'district' => 'Київський',
            'street' => 'Котляревського',
            'houseNumber' => '12',
            'apartmentNumber' => '33'];

        $address = $this->wrapper->address()->create($address_data);
        $this->assertEquals('Київський', $address->getDistrict());
    }

    public function testCreateAddressWithEntity()
    {
        $address = new Address(['postcode' => '07401',
            'country' => 'UA',
            'region' => 'Київська',
            'city' => 'Бровари',
            'district' => 'Київський',
            'street' => 'Котляревського',
            'houseNumber' => '12',
            'apartmentNumber' => '33']);

        $address = $this->wrapper->address()->create($address);
        $this->assertEquals('Київський', $address->getDistrict());

        return $address;
    }

    /**
     * @depends testCreateAddressWithEntity
     * @param Address $address
     */
    public function testGetAddressById($address)
    {
        $address = $this->wrapper->address()->getById($address->getId());
        $this->assertEquals('Київський', $address->getDistrict());
    }
}
