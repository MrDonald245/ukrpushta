<?php
/**
 * Created by eugene.
 * User: eugene
 * Date: 04/04/18
 * Time: 13:07
 */

require_once 'entities/Address.php';
require_once 'ClientWrapper.php';
require_once 'AddressWrapper.php';
require_once 'ShipmentWrapper.php';
require_once 'PrintFormWrapper.php';
require_once 'ShipmentGroupWrapper.php';

/**
 * Wraps the API for Ukrposhta
 * in order to make it easier to work with.
 */
class UkrposhtaApiWrapper
{
    /**
     * @var UkrposhtaApi $api
     */
    protected $api;

    /**
     * UkroshtaApiWrapper constructor.
     *
     * @param string $bearer
     * @param string $token
     */
    public function __construct($bearer, $token)
    {
        $this->api = new UkrposhtaApi($bearer, $token);
    }

    /**
     * @return ClientWrapper
     */
    public function client()
    {
        return new ClientWrapper($this->api->getBearer(), $this->api->getToken());
    }

    /**
     * @return AddressWrapper
     */
    public function address()
    {
        return new AddressWrapper($this->api->getBearer(), $this->api->getToken());
    }

    /**
     * @return ShipmentWrapper
     */
    public function shipment()
    {
        return new ShipmentWrapper($this->api->getBearer(), $this->api->getToken());
    }

    /**
     * @return ShipmentGroupWrapper
     */
    public function shipmentGroup()
    {
        return new ShipmentGroupWrapper($this->api->getBearer(), $this->api->getToken());
    }

    /**
     * @return PrintFormWrapper
     */
    public function printForm()
    {
        return new PrintFormWrapper($this->api->getBearer(), $this->api->getToken());
    }
    
    /**
     * @param EntityBase $entity
     * @return array|EntityBase
     */
    protected function entityToArray($entity)
    {
        return is_array($entity) ? $entity : $entity->toArray();
    }
}