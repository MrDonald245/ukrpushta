<?php
/**
 * Created by Eugene.
 * User: eugene
 * Date: 05/04/18
 * Time: 09:59
 */

require_once 'UkrposhtaApiWrapper.php';
require_once 'entities/Client.php';

/**
 * Class ClientWrapper is used for working with Ukrposhta API client.
 */
class ClientWrapper extends UkrposhtaApiWrapper
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
     * @param Client|array $client
     * @return Client
     */
    public function create($client)
    {
        $data = parent::entityToArray($client);
        $client_array = $this->api->method('POST')->params($data)->clients();

        return new Client($client_array);
    }

    /**
     * @param string $clientUuid
     * @param array $params
     * @return Client
     */
    public function edit($clientUuid, $params)
    {
        $client_array = $this->api->method('PUT')->params($params)->clients($clientUuid);
        return new Client($client_array);
    }

    /**
     * @param int $clientId
     * @return Client
     */
    public function getById($clientId)
    {
        $client_array = $this->api->method('GET')->action('getById')->clients($clientId);
        return new Client($client_array);
    }

    /**
     * @param int $clientExternalId
     * @return Client
     */
    public function getByExternalId($clientExternalId)
    {
        $client_array = $this->api->method('GET')->action('getByExternalId')->clients($clientExternalId);
        return new Client($client_array);
    }

    /**
     * @param string $clientPhoneNumber
     * @return Client
     */
    public function getByPhone($clientPhoneNumber)
    {
        $client_array = end($this->api
            ->method('GET')
            ->action('getByPhone')
            ->clients($clientPhoneNumber));

        return new Client($client_array);
    }

    /**
     * @param int $clientUuid
     * @return array $phones
     */
    public function getAllPhones($clientUuid)
    {
        return $this->api->method('GET')->action('getAllPhones')->clients($clientUuid);
    }

    /**
     * @param int $clientUuid
     * @return array $emails
     */
    public function getAllEmails($clientUuid)
    {
        return $this->api->method('GET')->action('getAllEmails')->clients($clientUuid);
    }

    /**
     * @param string $clientUuid
     * @param string $phoneNumber
     * @return Client $clientWithAddedPhones
     */
    public function addPhone($clientUuid, $phoneNumber)
    {
        $client_array = $this->api->method('PUT')->params(['phoneNumber' => $phoneNumber])->clients($clientUuid);
        return new Client($client_array);
    }

    /**
     * @param string $phoneUuid
     * @return void
     */
    public function deletePhone($phoneUuid)
    {
        $this->api->method('DELETE')->action('deletePhone')->clients($phoneUuid);
    }

    /**
     * @param string $clientUuid
     * @param int $addressId
     * @return Client
     */
    public function addAddress($clientUuid, $addressId)
    {
        $client_array = $this->api->method('PUT')->params(['addressId' => $addressId])->clients($clientUuid);
        return new Client($client_array);
    }

    /**
     * @param int $clientUuid
     * @return array $phones
     */
    public function getAllAddresses($clientUuid)
    {
        return $this->api->method('GET')->action('getAllAddresses')->clients($clientUuid);
    }

    /**
     * @param string $addressUuid
     * @return void
     */
    public function deleteAddress($addressUuid)
    {
        $this->api->method('DELETE')->action('deleteAddress')->clients($addressUuid);
    }
}