<?php
/**
 * Created by Eugene.
 * User: eugene
 * Date: 10/04/18
 * Time: 10:53
 */

require_once 'EntityBase.php';

class Shipment extends EntityBase
{
    const TYPE_EXPRESS = 'EXPRESS';
    const TYPE_STANDARD = 'STANDARD';

    const DELIVERY_TYPE_W2D = 'W2D';
    const DELIVERY_TYPE_W2W = 'W2W';
    const DELIVERY_TYPE_D2W = 'D2W';
    const DELIVERY_TYPE_D2D = 'D2D';

    const ONFAIL_TYPE_RETURN = 'RETURN';
    const ONFAIL_TYPE_RETURN_AFTER_FREE_STORAGE = 'RETURN_AFTER_FREE_STORAGE';
    const ONFAIL_TYPE_PROCESS_AS_REFUSAL = 'PROCESS_AS_REFUSAL';

    /**
     * Ідентифікатор створеного відправлення
     *
     * @var string $uuid
     */
    private $uuid;

    /**
     * Тип відправлення
     * EXPRESS - Укрпошта експрес.
     * STANDARD - Укрпошта стандарт. По замовченню EXPRESS
     *
     * @var string $type
     */
    private $type;

    /**
     * Інформацію про відправника можна вказати передавши uuid
     * відправника (дивись приклад мінімального запиту)
     * Якщо відправник юридична особа або фізична особа
     * підприємець то поля tin або edrpou повинні бути заповнені
     *
     * @var array $sender
     */
    private $sender;

    /**
     * Інформацію про одержувача можна вказати передавши uuid одержувача
     *
     * @var array $recipient
     */
    private $recipient;

    /**
     * Додатковий телефонний номер одержувача, якщо вказаний,
     * номер стає основним і відображається в документах
     *
     * @var string $recipientPhone
     */
    private $recipientPhone;

    /**
     * Додаткова електронна пошта
     *
     * @var string $recipientEmail
     */
    private $recipientEmail;

    /**
     * Id адреса отримувача, можна вказати тільки ту адресу, яка заповнена в тілі клієнта
     *
     * @var string $recipientAddressId
     */
    private $recipientAddressId;

    /**
     * Адреса повернення, може бути вказана додатково,
     * якщо не вказана returnAddressId буде використана основна адреса у якої main-true.
     * returnAddressId повинен бути одним з addressId відправника.
     *
     * @var string $returnAddressId
     */
    private $returnAddressId;

    /**
     * Ідентифікатор групи відправлень, якщо відправлення групове
     *
     * @var string $shipmentGroupUuid
     */
    private $shipmentGroupUuid;

    /**
     * Зовнішній ідентифікатор відправлення в базі контрагента
     *
     * @var string $externalId
     */
    private $externalId;

    /**
     * Тип доставки (4 основних типи:
     * W2D склад-двері, W2W склад-склад , D2W двері-склад, D2D двері- двері)
     * (Для Укрпошта STANDART(type:STANDA RT) тільки з оголошеною цінністю)
     *
     * @var string $deliveryType
     */
    private $deliveryType;

    /**
     * Тип посилки (для міжнародних відправлень)
     *
     * @var string $packageType
     */
    private $packageType;

    /**
     * Дії с відправленням в разі якщо одержувач не забрав його.
     * Якщо не вказано, RETURN по замовченню.
     * • RETURN - повернути відправнику.
     * • RETURN_AFTER_FREE_STORAGE - повернути після закінчення строку безкоштовного зберігання.
     * • PROCESS_AS_REFUSAL - знищити посилку.
     *
     * @var string $onFailReceiveType
     */
    private $onFailReceiveType;

    /**
     * Штрих-код посилки
     *
     * @var string $barcode
     */
    private $barcode;

    /**
     * Вага відправлення заповнюється з ваги вказаної в parcels
     *
     * @var int $weight
     */
    private $weight;

    /**
     * Найбільша сторона відправлення заповнюється як найбільше значення з довжини,
     * ширини або висоти вказаних в parcels.
     * Якщо length більше 50 см відправлення помічається як «громіздке».
     * Найбільша сторона не повинна перевищувати 2м,
     * сума довжини і найбільшого периметра у будь-якому напрямку (крім довжини) не повинна перевищувати 3,5 м.
     * Розраховується наступним чином: довжина + (2*(ширина + висота))
     *
     * @var int $length
     */
    private $length;

    /**
     * Ширина відправлення (тільки цифри). Вказується ширина в сантиметрах.
     *
     * @var int $width
     */
    private $width;

    /**
     * Висота відправлення (тільки цифри). Вказується висота в сантиметрах.
     *
     * @var int $height
     */
    private $height;

    /**
     * Заявлена ціна відправлення
     *
     * @var int $declaredPrice
     */
    private $declaredPrice;

    /**
     * Розрахована ціна доставки в гривнях, формується на основі тарифів ПАТ «Укрпошта»
     *
     * @var int $deliveryPrice
     */
    private $deliveryPrice;

    /**
     * Післяплата в гривнях, не може бути більшою ніж заявлена ціна
     *
     * @var int $postPay
     */
    private $postPay;

    /**
     * Код валюти (для міжнародних відправлень)
     *
     * @var string $currencyCode
     */
    private $currencyCode;

    /**
     * Курс валюти для післяплати (для міжнародних відправлень)
     *
     * @var float $postPayCurrencyCode
     */
    private $postPayCurrencyCode;

    /**
     * Курс валют (для міжнародних відправлень)
     *
     * @var float $currencyExchangeRate
     */
    private $currencyExchangeRate;

    /**
     * Клієнтська знижка якщо у клієнта або контрагента є знижка вона автоматично присвоюється відправленню.
     * Має: uuid- ідентифікатор, name-ім’я, fromDate-дата початку дії знижки, toDate-дата кінця дії знижки,
     * value- розмір знижки, shipmentType- тип відправлення на яке зазначається знижка,
     * applicableToDeliveryTypes- тип доставки – на яке призначається знижка.
     *
     * @var string $discount
     */
    private $discount;

    /**
     * Дата внесення останніх змін у відправлення.
     * Дата і час у форматі "2017-06-12T12:31:56"
     *
     * @var DateTime $lastModified
     */
    private $lastModified;

    /**
     * Опис чи коментарі (максимальна кількість 255 символів).
     *
     * @var string $description
     */
    private $description;

    /**
     * Параметри посилки. У відправлення може бути тільки одна посилка.
     * При створені посилки необхідно вказати основні поля:
     * weight - максимальна вага відправлення 30000 грам.
     * Вага відправлення повинна бути більше нуля,
     * length-довжина найбільшої сторони відправлення (тільки цифри),
     * вказується довжина в сантиметрах, довжина відправлення повинна бути більше нуля.
     * declaredPrice - Заявлена ціна відправлення, заповнюється в гривнях.
     * Параметри посилки використовуються як основні у відправленні
     *
     * @var array $parcels
     */
    private $parcels;

    /**
     * трекінг відправлення
     *
     * @var array $statusTrackings
     */
    private $statusTrackings;

    /**
     * Оплата за пересилання відправлення при отриманні.
     * True – оплата одержувачем false-оплата відправником.
     * По замовченню false (Для Укрпошта STANDART(type:STANDART) тільки з оголошеною цінністю)
     *
     * @var bool $paidByRecipient
     */
    private $paidByRecipient;

    /**
     * Оплата безготівковим розрахунком.
     * True- безготівковий, false – готівковий.
     * По замовченню true.
     *
     * @var bool $nonCashPayment
     */
    private $nonCashPayment;

    /**
     * Помітка громіздка посилка.
     * True-громіздка, false – не громіздка.
     * По замовченню false.
     * Якщо найбільша сторона відправлення більше 50 см, то присвоюється значення true.
     *
     * @var bool $bulky
     */
    private $bulky;

    /**
     * Помітка крихка посилка.
     * True-крихка, false – не крихка.
     * По замовченню false
     *
     * @var bool $fragile
     */
    private $fragile;

    /**
     * Помітка бджоли. По замовченню false
     *
     * @var bool $bees
     */
    private $bees;

    /**
     * Відправлення з повідомлення про отримання.
     * Якщо true при отриманні відправлення, відправник отримує лист про те що відправлення було отримано.
     * По замовченню false.
     *
     * @var bool $recommended
     */
    private $recommended;

    /**
     * Sms повідомлення про прибуття посилки.
     * Якщо true одержувач отримає повідомлення.
     * По замовченню false.
     *
     * @var bool $sms
     */
    private $sms;

    /**
     * Міжнародне відправлення по замовченню false.
     *
     * @var bool $international
     */
    private $international;

    /**
     * Життєвий цикл відправлення містить status, statusDate
     *
     * @var string $lifecycle
     */
    private $lifecycle;

    /**
     * Після створення відправлення status змінюється на CREATED,
     * після реєстрації відправлення у відділенні зв’язку status змінюється на REGISTERED
     *
     * @var string $status
     */
    private $status;

    /**
     * Ціна пересилання післяплати
     *
     * @var string $postPayDeliveryPrice
     */
    private $postPayDeliveryPrice;

    /**
     * Сторона яка сплачує плату за пересилання післяплати,
     * якщо true то суму сплачує одержувач, якщо false то сплачує відправник.
     * По замовченню postPayPaidByRecipient: true
     * (Для Укрпошта STANDART(type:STANDART) тільки з оголошеною цінністю)
     *
     * @var bool $postPayPaidByRecipient
     */
    private $postPayPaidByRecipient;

    /**
     * Опис калькуляції який описує на основі чого сформовано параметри вартості поштового відправлення.
     *
     * @var string $calculationDescription
     */
    private $calculationDescription;

    /**
     * Зворотна доставка документації.
     * По замовченню false.
     * (Для Укрпошта STANDART(type:STANDART) тільки з оголошеною цінністю)
     *
     * @var bool $documentBack
     */
    private $documentBack;

    /**
     * Огляд при вручені. По замовченню false.
     * (Доступно тільки для EXPRESS)
     *
     * @var bool $checkOnDelivery
     */
    private $checkOnDelivery;

    /**
     * Відобразити інформацію щодо банківського рахунку відправника на адресному ярлику.
     * По замовченню false.
     * Тільки якщо є післяплата postpay
     *
     * @var bool $transferPostPayToBankAccount
     */
    private $transferPostPayToBankAccount;

    /**
     * Плата за пересилання оплачена.
     * (Заповнюються робітником відділення)
     *
     * @var bool $deliveryPricePaid
     */
    private $deliveryPricePaid;

    /**
     * Післяплата оплачена. (Заповнюються робітником відділення)
     *
     * @var bool $postPayPaid
     */
    private $postPayPaid;

    /**
     * Плата за пересилання післяплати оплачена.
     * (Заповнюються робітником відділення)
     *
     * @var bool $postPayDeliveryPricePaid
     */
    private $postPayDeliveryPricePaid;

    /**
     * упаковка відправлення відправником,
     * якщо packedBySender true відправник отримує додаткову знижку 2%,
     * тільки для групових відправлень
     *
     * @var bool $packedBySender
     */
    private $packedBySender;

    /**
     * Напрямок відправлення, містить id - ідентифікатор, name - назву та description - опис
     *
     * @var array $direction
     */
    private $direction;


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
     * @param array $data
     * @return void
     */
    function initWithArray($data)
    {
        foreach ($this as $key => $value) {
            if (isset($data[$key])) {
                $this->$key = $data[$key];
            }
        }
    }

    /**
     * @return array
     */
    function toArray()
    {
        return parent::objectToArray();
    }

    /**
     * @return string Ідентифікатор створеного відправлення
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid Ідентифікатор створеного відправлення
     * @return Shipment
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @return string Тип відправлення
     * EXPRESS - Укрпошта експрес.
     * STANDARD - Укрпошта стандарт. По замовченню EXPRESS
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type Тип відправлення
     * EXPRESS - Укрпошта експрес.
     * STANDARD - Укрпошта стандарт. По замовченню EXPRESS
     *
     * @return Shipment
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return array Інформацію про відправника можна вказати передавши uuid
     * відправника (дивись приклад мінімального запиту)
     * Якщо відправник юридична особа або фізична особа
     * підприємець то поля tin або edrpou повинні бути заповнені
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param string $sender Інформацію про відправника можна вказати передавши uuid
     * відправника (дивись приклад мінімального запиту)
     * Якщо відправник юридична особа або фізична особа
     * підприємець то поля tin або edrpou повинні бути заповнені
     *
     * @return Shipment
     */
    public function setSender($sender)
    {
        $this->sender = ['uuid' => $sender];
        return $this;
    }

    /**
     * @return array Інформацію про одержувача можна вказати передавши uuid одержувача
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @param string $recipient Інформацію про одержувача можна вказати передавши uuid одержувача
     *
     * @return Shipment
     */
    public function setRecipient($recipient)
    {
        $this->recipient = ['uuid' => $recipient];
        return $this;
    }

    /**
     * @return string Додатковий телефонний номер одержувача, якщо вказаний,
     * номер стає основним і відображається в документах
     */
    public function getRecipientPhone()
    {
        return $this->recipientPhone;
    }

    /**
     * @param string $recipientPhone Додатковий телефонний номер одержувача, якщо вказаний,
     * номер стає основним і відображається в документах
     *
     * @return Shipment
     */
    public function setRecipientPhone($recipientPhone)
    {
        $this->recipientPhone = $recipientPhone;
        return $this;
    }

    /**
     * @return string Додаткова електронна пошта
     */
    public function getRecipientEmail()
    {
        return $this->recipientEmail;
    }

    /**
     * @param string $recipientEmail Додаткова електронна пошта
     *
     * @return Shipment
     */
    public function setRecipientEmail($recipientEmail)
    {
        $this->recipientEmail = $recipientEmail;
        return $this;
    }

    /**
     * @return string Id адреса отримувача, можна вказати тільки ту адресу, яка заповнена в тілі клієнта
     */
    public function getRecipientAddressId()
    {
        return $this->recipientAddressId;
    }

    /**
     * @param string $recipientAddressId
     * Id адреса отримувача, можна вказати тільки ту адресу, яка заповнена в тілі клієнта
     *
     * @return Shipment
     */
    public function setRecipientAddressId($recipientAddressId)
    {
        $this->recipientAddressId = $recipientAddressId;
        return $this;
    }

    /**
     * @return string Адреса повернення, може бути вказана додатково,
     * якщо не вказана returnAddressId буде використана основна адреса у якої main-true.
     * returnAddressId повинен бути одним з addressId відправника.
     */
    public function getReturnAddressId()
    {
        return $this->returnAddressId;
    }

    /**
     * @param string $returnAddressId Адреса повернення, може бути вказана додатково,
     * якщо не вказана returnAddressId буде використана основна адреса у якої main-true.
     * returnAddressId повинен бути одним з addressId відправника.
     *
     * @return Shipment
     */
    public function setReturnAddressId($returnAddressId)
    {
        $this->returnAddressId = $returnAddressId;
        return $this;
    }

    /**
     * @return string Ідентифікатор групи відправлень, якщо відправлення групове
     */
    public function getShipmentGroupUuid()
    {
        return $this->shipmentGroupUuid;
    }

    /**
     * @param string $shipmentGroupUuid Ідентифікатор групи відправлень, якщо відправлення групове
     *
     * @return Shipment
     */
    public function setShipmentGroupUuid($shipmentGroupUuid)
    {
        $this->shipmentGroupUuid = $shipmentGroupUuid;
        return $this;
    }

    /**
     * @return string Зовнішній ідентифікатор відправлення в базі контрагента
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @param string $externalId Зовнішній ідентифікатор відправлення в базі контрагента
     *
     * @return Shipment
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return string Тип доставки (4 основних типи:
     * W2D склад-двері, W2W склад-склад , D2W двері-склад, D2D двері- двері)
     * (Для Укрпошта STANDART(type:STANDA RT) тільки з оголошеною цінністю)
     */
    public function getDeliveryType()
    {
        return $this->deliveryType;
    }

    /**
     * @param string $deliveryType Тип доставки (4 основних типи:
     * W2D склад-двері, W2W склад-склад , D2W двері-склад, D2D двері- двері)
     * (Для Укрпошта STANDART(type:STANDA RT) тільки з оголошеною цінністю)
     *
     * @return Shipment
     */
    public function setDeliveryType($deliveryType)
    {
        $this->deliveryType = $deliveryType;
        return $this;
    }

    /**
     * @return string Тип посилки (для міжнародних відправлень)
     */
    public function getPackageType()
    {
        return $this->packageType;
    }

    /**
     * @param string $packageType Тип посилки (для міжнародних відправлень)
     *
     * @return Shipment
     */
    public function setPackageType($packageType)
    {
        $this->packageType = $packageType;
        return $this;
    }

    /**
     * @return string Дії с відправленням в разі якщо одержувач не забрав його.
     * Якщо не вказано, RETURN по замовченню.
     * • RETURN - повернути відправнику.
     * • RETURN_AFTER_FREE_STORAGE - повернути після закінчення строку безкоштовного зберігання.
     * • PROCESS_AS_REFUSAL - знищити посилку.
     */
    public function getOnFailReceiveType()
    {
        return $this->onFailReceiveType;
    }

    /**
     * @param string $onFailReceiveType Дії с відправленням в разі якщо одержувач не забрав його.
     * Якщо не вказано, RETURN по замовченню.
     * • RETURN - повернути відправнику.
     * • RETURN_AFTER_FREE_STORAGE - повернути після закінчення строку безкоштовного зберігання.
     * • PROCESS_AS_REFUSAL - знищити посилку.
     *
     * @return Shipment
     */
    public function setOnFailReceiveType($onFailReceiveType)
    {
        $this->onFailReceiveType = $onFailReceiveType;
        return $this;
    }

    /**
     * @return string Штрих-код посилки
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * @param string $barcode Штрих-код посилки
     *
     * @return Shipment
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;
        return $this;
    }

    /**
     * @return int Вага відправлення заповнюється з ваги вказаної в parcels
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param int $weight Вага відправлення заповнюється з ваги вказаної в parcels
     *
     * @return Shipment
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * @return int Найбільша сторона відправлення заповнюється як найбільше значення з довжини,
     * ширини або висоти вказаних в parcels.
     * Якщо length більше 50 см відправлення помічається як «громіздке».
     * Найбільша сторона не повинна перевищувати 2м,
     * сума довжини і найбільшого периметра у будь-якому напрямку (крім довжини) не повинна перевищувати 3,5 м.
     * Розраховується наступним чином: довжина + (2*(ширина + висота))
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param int $length Найбільша сторона відправлення заповнюється як найбільше значення з довжини,
     * ширини або висоти вказаних в parcels.
     * Якщо length більше 50 см відправлення помічається як «громіздке».
     * Найбільша сторона не повинна перевищувати 2м,
     * сума довжини і найбільшого периметра у будь-якому напрямку (крім довжини) не повинна перевищувати 3,5 м.
     * Розраховується наступним чином: довжина + (2*(ширина + висота))
     *
     * @return Shipment
     */
    public function setLength($length)
    {
        $this->length = $length;
        return $this;
    }

    /**
     * @return int Ширина відправлення (тільки цифри). Вказується ширина в сантиметрах.
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width Ширина відправлення (тільки цифри). Вказується ширина в сантиметрах.
     *
     * @return Shipment
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return int Висота відправлення (тільки цифри). Вказується висота в сантиметрах.
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height Висота відправлення (тільки цифри). Вказується висота в сантиметрах.
     *
     * @return Shipment
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @return int Заявлена ціна відправлення
     */
    public function getDeclaredPrice()
    {
        return $this->declaredPrice;
    }

    /**
     * @param int $declaredPrice Заявлена ціна відправлення
     *
     * @return Shipment
     */
    public function setDeclaredPrice($declaredPrice)
    {
        $this->declaredPrice = $declaredPrice;
        return $this;
    }

    /**
     * @return int Розрахована ціна доставки в гривнях, формується на основі тарифів ПАТ «Укрпошта»
     */
    public function getDeliveryPrice()
    {
        return $this->deliveryPrice;
    }

    /**
     * @param int $deliveryPrice Розрахована ціна доставки в гривнях, формується на основі тарифів ПАТ «Укрпошта»
     *
     * @return Shipment
     */
    public function setDeliveryPrice($deliveryPrice)
    {
        $this->deliveryPrice = $deliveryPrice;
        return $this;
    }

    /**
     * @return int Післяплата в гривнях, не може бути більшою ніж заявлена ціна
     */
    public function getPostPay()
    {
        return $this->postPay;
    }

    /**
     * @param int $postPay Післяплата в гривнях, не може бути більшою ніж заявлена ціна
     *
     * @return Shipment
     */
    public function setPostPay($postPay)
    {
        $this->postPay = $postPay;
        return $this;
    }

    /**
     * @return string Код валюти (для міжнародних відправлень)
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * @param string $currencyCode Код валюти (для міжнародних відправлень)
     *
     * @return Shipment
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;
        return $this;
    }

    /**
     * @return float Курс валюти для післяплати (для міжнародних відправлень)
     */
    public function getPostPayCurrencyCode()
    {
        return $this->postPayCurrencyCode;
    }

    /**
     * @param float $postPayCurrencyCode Курс валюти для післяплати (для міжнародних відправлень)
     *
     * @return Shipment
     */
    public function setPostPayCurrencyCode($postPayCurrencyCode)
    {
        $this->postPayCurrencyCode = $postPayCurrencyCode;
        return $this;
    }

    /**
     * @return float Курс валют (для міжнародних відправлень)
     */
    public function getCurrencyExchangeRate()
    {
        return $this->currencyExchangeRate;
    }

    /**
     * @param float $currencyExchangeRate Курс валют (для міжнародних відправлень)
     * @return Shipment
     */
    public function setCurrencyExchangeRate($currencyExchangeRate)
    {
        $this->currencyExchangeRate = $currencyExchangeRate;
        return $this;
    }

    /**
     * @return string Клієнтська знижка якщо у клієнта або контрагента є знижка вона автоматично присвоюється відправленню.
     * Має: uuid- ідентифікатор, name-ім’я, fromDate-дата початку дії знижки, toDate-дата кінця дії знижки,
     * value- розмір знижки, shipmentType- тип відправлення на яке зазначається знижка,
     * applicableToDeliveryTypes- тип доставки – на яке призначається знижка.
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param string $discount Клієнтська знижка якщо у клієнта або контрагента є знижка вона автоматично присвоюється відправленню.
     * Має: uuid- ідентифікатор, name-ім’я, fromDate-дата початку дії знижки, toDate-дата кінця дії знижки,
     * value- розмір знижки, shipmentType- тип відправлення на яке зазначається знижка,
     * applicableToDeliveryTypes- тип доставки – на яке призначається знижка.
     *
     * @return Shipment
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
        return $this;
    }

    /**
     * @return DateTime Дата внесення останніх змін у відправлення.
     * Дата і час у форматі "2017-06-12T12:31:56"
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * @param DateTime $lastModified Дата внесення останніх змін у відправлення.
     * Дата і час у форматі "2017-06-12T12:31:56"
     *
     * @return Shipment
     */
    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;
        return $this;
    }

    /**
     * @return string Опис чи коментарі (максимальна кількість 255 символів).
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description Опис чи коментарі (максимальна кількість 255 символів).
     *
     * @return Shipment
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return array Параметри посилки. У відправлення може бути тільки одна посилка.
     * При створені посилки необхідно вказати основні поля:
     * weight - максимальна вага відправлення 30000 грам.
     * Вага відправлення повинна бути більше нуля,
     * length-довжина найбільшої сторони відправлення (тільки цифри),
     * вказується довжина в сантиметрах, довжина відправлення повинна бути більше нуля.
     * declaredPrice - Заявлена ціна відправлення, заповнюється в гривнях.
     * Параметри посилки використовуються як основні у відправленні
     */
    public function getParcels()
    {
        return $this->parcels;
    }

    /**
     * @param array $parcels Параметри посилки. У відправлення може бути тільки одна посилка.
     * При створені посилки необхідно вказати основні поля:
     * weight - максимальна вага відправлення 30000 грам.
     * Вага відправлення повинна бути більше нуля,
     * length-довжина найбільшої сторони відправлення (тільки цифри),
     * вказується довжина в сантиметрах, довжина відправлення повинна бути більше нуля.
     * declaredPrice - Заявлена ціна відправлення, заповнюється в гривнях.
     * Параметри посилки використовуються як основні у відправленні
     *
     * @return Shipment
     */
    public function setParcels($parcels)
    {
        $this->parcels = $parcels;
        return $this;
    }

    /**
     * @return array трекінг відправлення
     */
    public function getStatusTrackings()
    {
        return $this->statusTrackings;
    }

    /**
     * @param array $statusTrackings трекінг відправлення
     *
     * @return Shipment
     */
    public function setStatusTrackings($statusTrackings)
    {
        $this->statusTrackings = $statusTrackings;
        return $this;
    }

    /**
     * @return bool Оплата за пересилання відправлення при отриманні.
     * True – оплата одержувачем false-оплата відправником.
     * По замовченню false (Для Укрпошта STANDART(type:STANDART) тільки з оголошеною цінністю)
     */
    public function isPaidByRecipient()
    {
        return $this->paidByRecipient;
    }

    /**
     * @param bool $paidByRecipient Оплата за пересилання відправлення при отриманні.
     * True – оплата одержувачем false-оплата відправником.
     * По замовченню false (Для Укрпошта STANDART(type:STANDART) тільки з оголошеною цінністю)
     *
     * @return Shipment
     */
    public function setPaidByRecipient($paidByRecipient)
    {
        $this->paidByRecipient = $paidByRecipient;
        return $this;
    }

    /**
     * @return bool Оплата безготівковим розрахунком.
     * True- безготівковий, false – готівковий.
     * По замовченню true
     */
    public function isNonCashPayment()
    {
        return $this->nonCashPayment;
    }

    /**
     * @param bool $nonCashPayment Оплата безготівковим розрахунком.
     * True- безготівковий, false – готівковий.
     * По замовченню true
     *
     * @return Shipment
     */
    public function setNonCashPayment($nonCashPayment)
    {
        $this->nonCashPayment = $nonCashPayment;
        return $this;
    }

    /**
     * @return bool Помітка громіздка посилка.
     * True-громіздка, false – не громіздка.
     * По замовченню false.
     * Якщо найбільша сторона відправлення більше 50 см, то присвоюється значення true.
     */
    public function isBulky()
    {
        return $this->bulky;
    }

    /**
     * @param bool $bulky Помітка громіздка посилка.
     * True-громіздка, false – не громіздка.
     * По замовченню false.
     * Якщо найбільша сторона відправлення більше 50 см, то присвоюється значення true.
     *
     * @return Shipment
     */
    public function setBulky($bulky)
    {
        $this->bulky = $bulky;
        return $this;
    }

    /**
     * @return bool Помітка крихка посилка.
     * True-крихка, false – не крихка.
     * По замовченню false
     */
    public function isFragile()
    {
        return $this->fragile;
    }

    /**
     * @param bool $fragile Помітка крихка посилка.
     * True-крихка, false – не крихка.
     * По замовченню false
     *
     * @return Shipment
     */
    public function setFragile($fragile)
    {
        $this->fragile = $fragile;
        return $this;
    }

    /**
     * @return bool Помітка бджоли. По замовченню false
     */
    public function isBees()
    {
        return $this->bees;
    }

    /**
     * @param bool $bees Помітка бджоли. По замовченню false
     *
     * @return Shipment
     */
    public function setBees($bees)
    {
        $this->bees = $bees;
        return $this;
    }

    /**
     * @return bool Відправлення з повідомлення про отримання.
     * Якщо true при отриманні відправлення, відправник отримує лист про те що відправлення було отримано.
     * По замовченню false.
     */
    public function isRecommended()
    {
        return $this->recommended;
    }

    /**
     * @param bool $recommended Відправлення з повідомлення про отримання.
     * Якщо true при отриманні відправлення, відправник отримує лист про те що відправлення було отримано.
     * По замовченню false.
     *
     * @return Shipment
     */
    public function setRecommended($recommended)
    {
        $this->recommended = $recommended;
        return $this;
    }

    /**
     * @return bool Sms повідомлення про прибуття посилки.
     * Якщо true одержувач отримає повідомлення.
     * По замовченню false.
     */
    public function isSms()
    {
        return $this->sms;
    }

    /**
     * @param bool $sms Sms повідомлення про прибуття посилки.
     * Якщо true одержувач отримає повідомлення.
     * По замовченню false.
     *
     * @return Shipment
     */
    public function setSms($sms)
    {
        $this->sms = $sms;
        return $this;
    }

    /**
     * @return bool Міжнародне відправлення по замовченню false.
     */
    public function isInternational()
    {
        return $this->international;
    }

    /**
     * @param bool $international Міжнародне відправлення по замовченню false.
     *
     * @return Shipment
     */
    public function setInternational($international)
    {
        $this->international = $international;
        return $this;
    }

    /**
     * @return string Життєвий цикл відправлення містить status, statusDate
     */
    public function getLifecycle()
    {
        return $this->lifecycle;
    }

    /**
     * @param string $lifecycle Життєвий цикл відправлення містить status, statusDate
     *
     * @return Shipment
     */
    public function setLifecycle($lifecycle)
    {
        $this->lifecycle = $lifecycle;
        return $this;
    }

    /**
     * @return string Після створення відправлення status змінюється на CREATED,
     * після реєстрації відправлення у відділенні зв’язку status змінюється на REGISTERED
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status Після створення відправлення status змінюється на CREATED,
     * після реєстрації відправлення у відділенні зв’язку status змінюється на REGISTERED
     *
     * @return Shipment
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string Ціна пересилання післяплати
     */
    public function getPostPayDeliveryPrice()
    {
        return $this->postPayDeliveryPrice;
    }

    /**
     * @param string $postPayDeliveryPrice Ціна пересилання післяплати
     *
     * @return Shipment
     */
    public function setPostPayDeliveryPrice($postPayDeliveryPrice)
    {
        $this->postPayDeliveryPrice = $postPayDeliveryPrice;
        return $this;
    }

    /**
     * @return bool Сторона яка сплачує плату Ні за пересилання післяплати,
     * якщо true то суму сплачує одержувач, якщо false то сплачує відправник.
     * По замовченню postPayPaidByRecipient: true
     * (Для Укрпошта STANDART(type:STANDART) тільки з оголошеною цінністю)
     */
    public function isPostPayPaidByRecipient()
    {
        return $this->postPayPaidByRecipient;
    }

    /**
     * @param bool $postPayPaidByRecipient Сторона яка сплачує плату Ні за пересилання післяплати,
     * якщо true то суму сплачує одержувач, якщо false то сплачує відправник.
     * По замовченню postPayPaidByRecipient: true
     * (Для Укрпошта STANDART(type:STANDART) тільки з оголошеною цінністю)
     *
     * @return Shipment
     */
    public function setPostPayPaidByRecipient($postPayPaidByRecipient)
    {
        $this->postPayPaidByRecipient = $postPayPaidByRecipient;
        return $this;
    }

    /**
     * @return string Опис калькуляції який описує на основі чого сформовано параметри вартості поштового відправлення.
     */
    public function getCalculationDescription()
    {
        return $this->calculationDescription;
    }

    /**
     * @param string $calculationDescription Опис калькуляції який описує на основі чого сформовано параметри
     * вартості поштового відправлення.
     *
     * @return Shipment
     */
    public function setCalculationDescription($calculationDescription)
    {
        $this->calculationDescription = $calculationDescription;
        return $this;
    }

    /**
     * @return bool Зворотна доставка документації.
     * По замовченню false.
     * (Для Укрпошта STANDART(type:STANDART) тільки з оголошеною цінністю)
     */
    public function isDocumentBack()
    {
        return $this->documentBack;
    }

    /**
     * @param bool $documentBack
     *  Зворотна доставка документації.
     * По замовченню false.
     * (Для Укрпошта STANDART(type:STANDART) тільки з оголошеною цінністю)
     *
     * @return Shipment
     */
    public function setDocumentBack($documentBack)
    {
        $this->documentBack = $documentBack;
        return $this;
    }

    /**
     * @return bool Огляд при вручені. По замовченню false.
     * (Доступно тільки для EXPRESS)
     */
    public function isCheckOnDelivery()
    {
        return $this->checkOnDelivery;
    }

    /**
     * @param bool $checkOnDelivery Огляд при вручені. По замовченню false.
     * (Доступно тільки для EXPRESS)
     *
     * @return Shipment
     */
    public function setCheckOnDelivery($checkOnDelivery)
    {
        $this->checkOnDelivery = $checkOnDelivery;
        return $this;
    }

    /**
     * @return bool Відобразити інформацію щодо банківського рахунку відправника на адресному ярлику.
     * По замовченню false.
     * Тільки якщо є післяплата postpay
     */
    public function isTransferPostPayToBankAccount()
    {
        return $this->transferPostPayToBankAccount;
    }

    /**
     * @param bool $transferPostPayToBankAccount Відобразити інформацію щодо банківського рахунку відправника на адресному ярлику.
     * По замовченню false.
     * Тільки якщо є післяплата postpay
     * @return Shipment
     */
    public function setTransferPostPayToBankAccount($transferPostPayToBankAccount)
    {
        $this->transferPostPayToBankAccount = $transferPostPayToBankAccount;
        return $this;
    }

    /**
     * @return bool Плата за пересилання оплачена.
     * (Заповнюються робітником відділення)
     */
    public function isDeliveryPricePaid()
    {
        return $this->deliveryPricePaid;
    }

    /**
     * @param bool $deliveryPricePaid Плата за пересилання оплачена.
     * (Заповнюються робітником відділення)
     *
     * @return Shipment
     */
    public function setDeliveryPricePaid($deliveryPricePaid)
    {
        $this->deliveryPricePaid = $deliveryPricePaid;
        return $this;
    }

    /**
     * @return bool Післяплата оплачена. (Заповнюються робітником відділення)
     */
    public function isPostPayPaid()
    {
        return $this->postPayPaid;
    }

    /**
     * @param bool $postPayPaid Післяплата оплачена. (Заповнюються робітником відділення)
     * @return Shipment
     */
    public function setPostPayPaid($postPayPaid)
    {
        $this->postPayPaid = $postPayPaid;
        return $this;
    }

    /**
     * @return bool Плата за пересилання післяплати оплачена.
     * (Заповнюються робітником відділення)
     */
    public function isPostPayDeliveryPricePaid()
    {
        return $this->postPayDeliveryPricePaid;
    }

    /**
     * @param bool $postPayDeliveryPricePaid Плата за пересилання післяплати оплачена.
     * (Заповнюються робітником відділення)
     *
     * @return Shipment
     */
    public function setPostPayDeliveryPricePaid($postPayDeliveryPricePaid)
    {
        $this->postPayDeliveryPricePaid = $postPayDeliveryPricePaid;
        return $this;
    }

    /**
     * @return bool упаковка відправлення відправником,
     * якщо packedBySender true відправник отримує додаткову знижку 2%,
     * тільки для групових відправлень
     */
    public function isPackedBySender()
    {
        return $this->packedBySender;
    }

    /**
     * @param bool $packedBySender упаковка відправлення відправником,
     * якщо packedBySender true відправник отримує додаткову знижку 2%,
     * тільки для групових відправлень
     *
     * @return Shipment
     */
    public function setPackedBySender($packedBySender)
    {
        $this->packedBySender = $packedBySender;
        return $this;
    }

    /**
     * @return array Напрямок відправлення, містить id - ідентифікатор, name - назву та description - опис
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @param array $direction Напрямок відправлення,
     * містить id - ідентифікатор, name - назву та description - опис
     *
     * @return Shipment
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
        return $this;
    }
}