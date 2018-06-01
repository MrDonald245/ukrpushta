<?php
/**
 * Created by Eugene.
 * User: eugene
 * Date: 11/04/18
 * Time: 12:27
 */

require_once 'UkrposhtaApiWrapper.php';

class PrintFormWrapper extends UkrposhtaApiWrapper
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
     * @deprecated
     * @param string $shipmentUuid
     * @return string pdf contents
     */
    public function shipmentLabel($shipmentUuid)
    {
        return $this->api->method('GET')
            ->action('shipmentLabel')
            ->printForm($shipmentUuid);
    }

    /**
     * @param string $shipmentUuid
     * @return string pdf contents
     */
    public function shipmentSticker($shipmentUuid)
    {
        return $this->api->method('GET')
            ->action('shipmentSticker')
            ->printForm($shipmentUuid);
    }

    /**
     * @param string $shipmentGroupUuid
     * @return string pdf contents
     */
    public function shipmentGroupLabel($shipmentGroupUuid)
    {
        return $this->api->method('GET')
            ->action('shipmentGroupLabel')
            ->printForm($shipmentGroupUuid);
    }

    /**
     * @param string $shipmentGroupUuid
     * @return string pdf contents
     */
    public function shipmentGroupSticker($shipmentGroupUuid)
    {
        return $this->api->method('GET')
            ->action('shipmentGroupSticker')
            ->printForm($shipmentGroupUuid);
    }

    /**
     * @param string $shipmentGroupUuid
     * @return string pdf contents
     */
    public function shipmentGroup103a($shipmentGroupUuid)
    {
        return $this->api->method('GET')
            ->action('shipmentGroup103a')
            ->printForm($shipmentGroupUuid);
    }
}