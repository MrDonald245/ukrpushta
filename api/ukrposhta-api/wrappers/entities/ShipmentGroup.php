<?php
/**
 * Created by Eugene.
 * User: eugene
 * Date: 10/04/18
 * Time: 15:10
 */

require_once 'EntityBase.php';

class ShipmentGroup extends EntityBase
{
    /**
     * Ідентифікатор створеної групи
     *
     * @var string $uuid
     */
    private $uuid;

    /**
     * Ім’я групи
     *
     * @var string $name
     */
    private $name;

    /**
     * Ідентифікатор клієнта
     *
     * @var string $clientUuid
     */
    private $clientUuid;

    /**
     * Тип групи поштових відправлень EXPRESS або STANDARD, по замовченню EXPRESS
     *
     * @var string $type
     */
    private $type;

    /**
     * EntityBase constructor.
     *
     * @param array|string|null $data Could be as an array, json, or null
     */
    public function __construct($data = null)
    {
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        if ($data != null) {
            $this->initWithArray($data);
        }
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ShipmentGroup
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getClientUuid()
    {
        return $this->clientUuid;
    }

    /**
     * @param string $clientUuid
     * @return ShipmentGroup
     */
    public function setClientUuid($clientUuid)
    {
        $this->clientUuid = $clientUuid;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return ShipmentGroup
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->objectToArray();
    }

    /**
     * @param array $data
     * @return void
     */
    public function initWithArray($data)
    {
        foreach ($this as $key => $value)
        {
            if (isset($data[$key])) {
                $this->$key = $data[$key];
            }
        }
    }
}