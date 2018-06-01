<?php
/**
 * Created by Eugene.
 * User: eugene
 * Date: 05/04/18
 * Time: 09:48
 */

require_once 'EntityBase.php';

class Client extends EntityBase
{
    const TYPE_INDIVIDUAL = 'INDIVIDUAL';
    const TYPE_COMPANY = 'COMPANY';
    const TYPE_PRIVATE_ENTREPRENEUR = 'PRIVATE_ENTREPRENEUR';

    /**
     * @var string $uuid
     */
    private $uuid;

    /**
     * Тип клієнта.
     * INDIVIDUAL – фізична особа
     * COMPANY – юридична особа
     * PRIVATE_ENTREPRENEUR – фізична особа підприємець.
     * По замовченню тип COMPANY.
     * Тип клієнта не можна змінити.
     *
     * @var string $type
     */
    private $type;

    /**
     * Ім’я клієнта, (максимальна кількість символів 250,
     * є обов’язковим для юридичної особи
     * та фізичної особи підприємця, для фізичної особи формується
     * з параметрів: firstName, middleName , lastName)
     *
     * @var string
     */
    private $name;

    /**
     * Ім’я фізичної особи (максимальна кількість символів 250, мінімальна 2)
     *
     * @var string $firstName
     */
    private $firstName;

    /**
     * По батькові фізичної особи (максимальна кількість символів 250, мінімальна 2)
     *
     * @var string $middleName
     */
    private $middleName;

    /**
     * Фамілія фізичної особи (максимальна кількість символів 250)
     *
     * @var string $lastName
     */
    private $lastName;

    /**
     * Унікальний реєстраційний номер
     *
     * @var string $uniqueRegistrationNumber
     */
    private $uniqueRegistrationNumber;

    /**
     * Ідентифікатор адреси, вказується Id попередньо створеної адреси
     *
     * @var int $addressId
     */
    private $addressId;

    /**
     * Якщо клієнт вказав декілька адрес,
     * буде використовуватись та у якій main- true.
     * Тип адреси (PHYSICAL,LEGAL)
     *
     * @var array $addresses
     */
    private $addresses;

    /**
     * Змінна яка вказує чи є клієнт юридичної або фізичною особою.
     * Уюридичної особи individual повинен бути-false,
     * у фізичної-true. (Буде видалений )
     *
     * @var bool $individual
     */
    private $individual;

    /**
     * Телефонний номер клієнта (тільки цифри, максимальна кількість 25 символів)
     *
     * @var string $phoneNumber
     */
    private $phoneNumber;

    /**
     * Якщо клієнт вказав декілька телефонів,
     * буде використовуватись той у якого main -true.
     * Type – тип телефонного номера клієнта (WORK, PERSONAL).
     * Uuid- ідентифікатор.
     *
     * @var array $phones
     */
    private $phones;

    /**
     * МФО код клієнта, (тільки цифри, максимальна кількість 6 символів), тільки діючи банки.
     *
     * @var string $bankCode
     */
    private $bankCode;

    /**
     * Розрахунковий рахунок, (тільки цифри, від 6 до 14 символів), перевірка на валідність.
     *
     * @var string $bankAccount
     */
    private $bankAccount;

    /**
     * Ідентифікатор контрагента який створив клієнта
     *
     * @var int $counterpartyUuid
     */
    private $counterpartyUuid;

    /**
     * Електрона пошта клієнта
     *
     * @var string $email
     */
    private $email;

    /**
     * Якщо клієнт вказав декілька електронних пошти, буде використовуватись та у якої main - true
     *
     * @var array $emails
     */
    private $emails;

    /**
     * Унікальний ідентифікатор клієнта що надається ПАТ «Укрпошта»
     *
     * @var string $postId
     */
    private $postId;

    /**
     * Зовнішній ідентифікатор клієнта в базі контрагента
     *
     * @var string $externalId
     */
    private $externalId;

    /**
     * Ім’я контактної особи
     *
     * @var string $contactPersonName
     */
    private $contactPersonName;

    /**
     * Резидент України. Якщо resident - true то клієнт є резидентом України. По замовченню при створенні клієнта resident – true.
     *
     * @var bool $resident
     */
    private $resident;

    /**
     * ЄДРПОУ юридичної особи (тільки цифри, 5-8 символів).
     * Може бути збережений тільки валідний ЄДРПОУ.
     *
     * @var string $edrpou
     */
    private $edrpou;

    /**
     * Індивідуальний податковий номер для фізичних осіб та фізичних осіб підприємців
     * (тільки цифри, для фізичних осіб 10, для фізичних осіб підприємців 12 символів).
     * Може бути збережений тільки валідний ІПН.
     *
     * @var string
     */
    private $tin;

    /**
     * EntityBase constructor.
     *
     * @param array|string|null $data
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
     * @return string Тип клієнта.
     * INDIVIDUAL – фізична особа
     * COMPANY – юридична особа
     * PRIVATE_ENTREPRENEUR – фізична особа підприємець.
     * По замовченню тип COMPANY.
     * Тип клієнта не можна змінити.
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type Тип клієнта.
     * INDIVIDUAL – фізична особа
     * COMPANY – юридична особа
     * PRIVATE_ENTREPRENEUR – фізична особа підприємець.
     * По замовченню тип COMPANY.
     * Тип клієнта не можна змінити.
     * @return Client
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string Ім’я клієнта, (максимальна кількість символів 250,
     * є обов’язковим для юридичної особи
     * та фізичної особи підприємця, для фізичної особи формується
     * з параметрів: firstName, middleName , lastName)
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name Ім’я клієнта, (максимальна кількість символів 250,
     * є обов’язковим для юридичної особи
     * та фізичної особи підприємця, для фізичної особи формується
     * з параметрів: firstName, middleName , lastName)
     * @return Client
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string Ім’я фізичної особи (максимальна кількість символів 250, мінімальна 2)
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName Ім’я фізичної особи (максимальна кількість символів 250, мінімальна 2)
     * @return Client
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string По батькові фізичної особи (максимальна кількість символів 250, мінімальна 2)
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @param string $middleName По батькові фізичної особи (максимальна кількість символів 250, мінімальна 2)
     * @return Client
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;
        return $this;
    }

    /**
     * @return string Фамілія фізичної особи (максимальна кількість символів 250)
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName Фамілія фізичної особи (максимальна кількість символів 250)
     * @return Client
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string Унікальний реєстраційний номер
     */
    public function getUniqueRegistrationNumber()
    {
        return $this->uniqueRegistrationNumber;
    }

    /**
     * @param string $uniqueRegistrationNumber
     * @return Client
     */
    public function setUniqueRegistrationNumber($uniqueRegistrationNumber)
    {
        $this->uniqueRegistrationNumber = $uniqueRegistrationNumber;
        return $this;
    }

    /**
     * @return int Ідентифікатор адреси, вказується Id попередньо створеної адреси
     */
    public function getAddressId()
    {
        return $this->addressId;
    }

    /**
     * @param int $addressId Ідентифікатор адреси, вказується Id попередньо створеної адреси
     * @return Client
     */
    public function setAddressId($addressId)
    {
        $this->addressId = $addressId;
        return $this;
    }

    /**
     * @return array Якщо клієнт вказав декілька адрес,
     * буде використовуватись та у якій main- true.
     * Тип адреси (PHYSICAL,LEGAL)
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * @param array $addresses Якщо клієнт вказав декілька адрес,
     * буде використовуватись та у якій main- true.
     * Тип адреси (PHYSICAL,LEGAL)
     * @return Client
     */
    public function setAddresses($addresses)
    {
        $this->addresses = $addresses;
        return $this;
    }

    /**
     * @return bool Змінна яка вказує чи є клієнт юридичної або фізичною особою.
     * Уюридичної особи individual повинен бути-false,
     * у фізичної-true. (Буде видалений )
     */
    public function isIndividual()
    {
        return $this->individual;
    }

    /**
     * @param bool $individual Змінна яка вказує чи є клієнт юридичної або фізичною особою.
     * Уюридичної особи individual повинен бути-false,
     * у фізичної-true. (Буде видалений )
     * @return Client
     */
    public function setIndividual($individual)
    {
        $this->individual = $individual;
        return $this;
    }

    /**
     * @return string Телефонний номер клієнта (тільки цифри, максимальна кількість 25 символів)
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber Телефонний номер клієнта (тільки цифри, максимальна кількість 25 символів)
     * @return Client
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * @return array Якщо клієнт вказав декілька телефонів,
     * буде використовуватись той у якого main -true.
     * Type – тип телефонного номера клієнта (WORK, PERSONAL).
     * Uuid- ідентифікатор.
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * @param array $phones Якщо клієнт вказав декілька телефонів,
     * буде використовуватись той у якого main -true.
     * Type – тип телефонного номера клієнта (WORK, PERSONAL).
     * Uuid- ідентифікатор.
     * @return Client
     */
    public function setPhones($phones)
    {
        $this->phones = $phones;
        return $this;
    }

    /**
     * @return string МФО код клієнта, (тільки цифри, максимальна кількість 6 символів), тільки діючи банки.
     */
    public function getBankCode()
    {
        return $this->bankCode;
    }

    /**
     * @param string $bankCode МФО код клієнта, (тільки цифри, максимальна кількість 6 символів), тільки діючи банки.
     * @return Client
     */
    public function setBankCode($bankCode)
    {
        $this->bankCode = $bankCode;
        return $this;
    }

    /**
     * @return string Розрахунковий рахунок, (тільки цифри, від 6 до 14 символів), перевірка на валідність.
     */
    public function getBankAccount()
    {
        return $this->bankAccount;
    }

    /**
     * @param string $bankAccount Розрахунковий рахунок, (тільки цифри, від 6 до 14 символів), перевірка на валідність.
     * @return Client
     */
    public function setBankAccount($bankAccount)
    {
        $this->bankAccount = $bankAccount;
        return $this;
    }

    /**
     * @return int Ідентифікатор контрагента який створив клієнта
     */
    public function getCounterpartyUuid()
    {
        return $this->counterpartyUuid;
    }

    /**
     * @param int $counterpartyUuid Ідентифікатор контрагента який створив клієнта
     * @return Client
     */
    public function setCounterpartyUuid($counterpartyUuid)
    {
        $this->counterpartyUuid = $counterpartyUuid;
        return $this;
    }

    /**
     * @return string Електрона пошта клієнта
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email Електрона пошта клієнта
     * @return Client
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return array Якщо клієнт вказав декілька електронних пошти, буде використовуватись та у якої main - true
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * @param array $emails Якщо клієнт вказав декілька електронних пошти, буде використовуватись та у якої main - true
     * @return Client
     */
    public function setEmails($emails)
    {
        $this->emails = $emails;
        return $this;
    }

    /**
     * @return string Унікальний ідентифікатор клієнта що надається ПАТ «Укрпошта»
     */
    public function getPostId()
    {
        return $this->postId;
    }

    /**
     * @param string $postId Унікальний ідентифікатор клієнта що надається ПАТ «Укрпошта»
     * @return Client
     */
    public function setPostId($postId)
    {
        $this->postId = $postId;
        return $this;
    }

    /**
     * @return string Зовнішній ідентифікатор клієнта в базі контрагента
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @param string $externalId Зовнішній ідентифікатор клієнта в базі контрагента
     * @return Client
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return string Ім’я контактної особи
     */
    public function getContactPersonName()
    {
        return $this->contactPersonName;
    }

    /**
     * @param string $contactPersonName Ім’я контактної особи
     * @return Client
     */
    public function setContactPersonName($contactPersonName)
    {
        $this->contactPersonName = $contactPersonName;
        return $this;
    }

    /**
     * @return bool Резидент України.
     * Якщо resident - true то клієнт є резидентом України.
     * По замовченню при створенні клієнта resident – true.
     */
    public function isResident()
    {
        return $this->resident;
    }

    /**
     * @param bool $resident Резидент України.
     * Якщо resident - true то клієнт є резидентом України.
     * По замовченню при створенні клієнта resident – true.
     * @return Client
     */
    public function setResident($resident)
    {
        $this->resident = $resident;
        return $this;
    }

    /**
     * @return string ЄДРПОУ юридичної особи (тільки цифри, 5-8 символів).
     * Може бути збережений тільки валідний ЄДРПОУ.
     */
    public function getEdrpou()
    {
        return $this->edrpou;
    }

    /**
     * @param string $edrpou ЄДРПОУ юридичної особи (тільки цифри, 5-8 символів).
     * Може бути збережений тільки валідний ЄДРПОУ.
     * @return Client
     */
    public function setEdrpou($edrpou)
    {
        $this->edrpou = $edrpou;
        return $this;
    }

    /**
     * @return string Індивідуальний податковий номер для фізичних осіб та фізичних осіб підприємців
     * (тільки цифри, для фізичних осіб 10, для фізичних осіб підприємців 12 символів).
     * Може бути збережений тільки валідний ІПН.
     */
    public function getTin()
    {
        return $this->tin;
    }

    /**
     * @param string $tin Індивідуальний податковий номер для фізичних осіб та фізичних осіб підприємців
     * (тільки цифри, для фізичних осіб 10, для фізичних осіб підприємців 12 символів).
     * Може бути збережений тільки валідний ІПН.
     * @return Client
     */
    public function setTin($tin)
    {
        $this->tin = $tin;
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
        foreach ($this as $key => $value) {
            if (isset($data[$key])) {
                $this->$key = $data[$key];
            }
        }
    }
}