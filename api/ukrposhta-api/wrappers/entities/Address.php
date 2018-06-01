<?php
/**
 * Created by Eugene.
 * User: eugene
 * Date: 04/04/18
 * Time: 13:30
 */

require_once 'EntityBase.php';

/**
 * Address entity for UkrposhtaApiWrapper
 *
 */
class Address extends EntityBase
{
    /**
     * Унікальний ідентифікатор адреси - присвоюється автоматично при створенні.
     *
     * @var int $id
     */
    private $id;

    /**
     * Поштовий індекс (тільки цифри 5 символів)
     *
     * @var string $postcode
     */
    private $postcode;

    /**
     * Країна по замовченню UA
     *
     * @var string $country
     */
    private $country;

    /**
     * Назва області (максимальна кількість 45 символів)
     *
     * @var string $region
     */
    private $region;

    /**
     * Назва району (максимальна кількість 45 символів)
     *
     * @var string $district
     */
    private $district;

    /**
     * Назва населеного пункту (максимальна кількість 45 символів)
     *
     * @var string $city ;
     */
    private $city;

    /**
     * Назва вулиці (максимальна кількість 255 символів)
     *
     * @var string $street
     */
    private $street;

    /**
     * Номер будинку (максимальна кількість 15 символів)
     *
     * @var string $houseNumber
     */
    private $houseNumber;

    /**
     * Номер квартири (максимальна кількість 15 символів)
     *
     * @var string $apartmentNumber
     */
    private $apartmentNumber;

    /**
     * Признак сільської місцевості true/false.
     * Використовується для прорахунку тарифікації,
     * присвоюється автоматично на основі індексу.
     *
     * @var bool $countryside
     */
    private $countryside;

    /**
     * опис чи коментарі (максимальна кількість 255 символів).
     *
     * @var string $description
     */
    private $description;

    /**
     * Частини адреси зібрані в рядок через кому
     *
     * @var string $detailedInfo
     */
    private $detailedInfo;

    /**
     * EntityBase constructor.
     *
     * @param array|string|null data Could be as an array, json, or null
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
     * @return int $id Унікальний ідентифікатор адреси - присвоюється автоматично при створенні.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id Унікальний ідентифікатор адреси - присвоюється автоматично при створенні.
     * @return Address
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string Поштовий індекс (тільки цифри 5 символів)
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * @param string $postcode Поштовий індекс (тільки цифри 5 символів)
     * @return Address
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;
        return $this;
    }

    /**
     * @return string Країна по замовченню UA
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country Країна по замовченню UA
     * @return Address
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return string Назва області (максимальна кількість 45 символів)
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param string $region Назва області (максимальна кількість 45 символів)
     * @return Address
     */
    public function setRegion($region)
    {
        $this->region = $region;
        return $this;
    }

    /**
     * @return string Назва району (максимальна кількість 45 символів)
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @param string $district Назва району (максимальна кількість 45 символів)
     * @return Address
     */
    public function setDistrict($district)
    {
        $this->district = $district;
        return $this;
    }

    /**
     * @return string Назва населеного пункту (максимальна кількість 45 символів)
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city Назва населеного пункту (максимальна кількість 45 символів)
     * @return Address
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string Назва вулиці (максимальна кількість 255 символів)
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param string $street Назва вулиці (максимальна кількість 255 символів)
     * @return Address
     */
    public function setStreet($street)
    {
        $this->street = $street;
        return $this;
    }

    /**
     * @return string Номер будинку (максимальна кількість 15 символів)
     */
    public function getHouseNumber()
    {
        return $this->houseNumber;
    }

    /**
     * @param string $houseNumber Номер будинку (максимальна кількість 15 символів)
     * @return Address
     */
    public function setHouseNumber($houseNumber)
    {
        $this->houseNumber = $houseNumber;
        return $this;
    }

    /**
     * @return string Номер квартири (максимальна кількість 15 символів)
     */
    public function getApartmentNumber()
    {
        return $this->apartmentNumber;
    }

    /**
     * @param string $apartmentNumber Номер квартири (максимальна кількість 15 символів)
     * @return Address
     */
    public function setApartmentNumber($apartmentNumber)
    {
        $this->apartmentNumber = $apartmentNumber;
        return $this;
    }

    /**
     * @return bool Признак сільської місцевості true/false.
     * Використовується для прорахунку тарифікації,
     * присвоюється автоматично на основі індексу.
     */
    public function isCountryside()
    {
        return $this->countryside;
    }

    /**
     * @param bool $countryside Признак сільської місцевості true/false.
     * Використовується для прорахунку тарифікації,
     * присвоюється автоматично на основі індексу.
     * @return Address
     */
    public function setCountryside($countryside)
    {
        $this->countryside = $countryside;
        return $this;
    }

    /**
     * @return string опис чи коментарі (максимальна кількість 255 символів).
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description опис чи коментарі (максимальна кількість 255 символів).
     * @return Address
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string Частини адреси зібрані в рядок через кому
     */
    public function getDetailedInfo()
    {
        return $this->detailedInfo;
    }

    /**
     * @param string $detailedInfo Частини адреси зібрані в рядок через кому
     * @return Address
     */
    public function setDetailedInfo($detailedInfo)
    {
        $this->detailedInfo = $detailedInfo;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return parent::objectToArray();
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