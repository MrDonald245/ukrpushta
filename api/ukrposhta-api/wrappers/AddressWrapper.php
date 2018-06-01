<?php
/**
 * Created by Eugene.
 * User: eugene
 * Date: 05/04/18
 * Time: 10:07
 */

require_once 'UkrposhtaApiWrapper.php';

/**
 * Class AddressWrapper is used for working with
 */
class AddressWrapper extends UkrposhtaApiWrapper
{
    /**
     * @param string $bearer
     * @param string $token
     */
    public function __construct($bearer, $token)
    {
        parent::__construct($bearer, $token);
    }


    /**
     * @param Address|array $address
     * @return Address
     */
    public function create($address)
    {
        $data = $this->entityToArray($address);
        $address_array = $this->api->method('POST')->params($data)->addresses();

        return new Address($address_array);
    }

    /**
     * @param int $addressId
     * @return Address
     */
    public function getById($addressId)
    {
        $address_array = $this->api->method('GET')->addresses($addressId);

        return new Address($address_array);
    }
}