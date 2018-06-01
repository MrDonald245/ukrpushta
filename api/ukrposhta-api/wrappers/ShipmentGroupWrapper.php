<?php
/**
 * Created by Eugene.
 * User: eugene
 * Date: 10/04/18
 * Time: 15:17
 */


require_once 'UkrposhtaApiWrapper.php';
require_once 'entities/ShipmentGroup.php';

class ShipmentGroupWrapper extends UkrposhtaApiWrapper
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
     * @param ShipmentGroup|array $shipmentGroup
     * @return ShipmentGroup
     */
    public function create($shipmentGroup)
    {
        $params = $this->entityToArray($shipmentGroup);
        $shipment_group_result = $this->api->method('POST')
            ->action('create')->params($params)->shipmentGroups();

        return new ShipmentGroup($shipment_group_result);
    }

    /**
     * @param int $shipmentGroupUuid
     * @param array $params
     * @return ShipmentGroup
     */
    public function edit($shipmentGroupUuid, $params)
    {
        $shipment_group_array = $this->api->method('PUT')
            ->params($params)
            ->shipmentGroups($shipmentGroupUuid);

        return new ShipmentGroup($shipment_group_array);
    }

    /**
     * @param string $shipmentUuid
     * @param string $shipmentGroupUuid
     * @return array with message
     */
    public function addShipment($shipmentUuid, $shipmentGroupUuid)
    {
        $shipment_group_array = $this->api->method('POST')
            ->action('addShipment')
            ->shipmentGroups($shipmentGroupUuid, $shipmentUuid);

        return $shipment_group_array;
    }

    /**
     * @param string $shipmentGroupUuid
     * @return ShipmentGroup
     */
    public function get($shipmentGroupUuid)
    {
        $shipment_group_array = $this->api->method('GET')
            ->action('get')
            ->shipmentGroups($shipmentGroupUuid);

        return new ShipmentGroup($shipment_group_array);
    }

    /**
     * @param string $clientUuid
     * @return ShipmentGroup[]
     */
    public function getByClientUuid($clientUuid)
    {
        $shipment_groups_array = $this->api->method('GET')
            ->action('getByClientUuid')
            ->shipmentGroups($clientUuid);

        $shipment_groups = [];
        foreach ($shipment_groups_array as $shipment_group_array) {
            $shipment_groups[] = new ShipmentGroup($shipment_group_array);
        }

        return $shipment_groups;
    }

    /**
     * @param string $shipmentUuid
     * @return void
     */
    public function deleteShipment($shipmentUuid)
    {
        $this->api->method('DELETE')->shipmentGroups($shipmentUuid);
    }
}