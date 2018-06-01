<?php

/** @noinspection PhpUndefinedFieldInspection */
/** @noinspection PhpRedundantCatchClauseInspection */

/**
 * Created by Eugene.
 * User: eugene
 * Date: 16/04/18
 * Time: 09:10
 */

ini_set('display_errors', false);

require_once('../../api/Simpla.php');
require_once('../../api/ukrposhta-api/kernel/UkrposhtaApi.php');
require_once('../../api/ukrposhta-api/wrappers/UkrposhtaApiWrapper.php');

class UkrposhtaEnGenerator
{
    /**
     * @var Simpla $simpla
     */
    private $simpla;

    /**
     * @var array $senderInfo
     */
    private $senderInfo = [
        'phone'       => '',
        'postcode'    => '',
        'type'        => '',
        'first_name'  => '',
        'last_name'   => '',
        'name'        => '',
        'tin'         => '',
        'middle_name' => '',
        'edrpou'      => '',
    ];

    /**
     * @var array $recipientInfo
     */
    private $recipientInfo = [
        'postcode'     => '',
        'bank_code'    => '',
        'bank_account' => '',
        'firstName'    => '',
        'lastName'     => '',
        'phone'        => '',
        'email'        => '',
    ];

    /**
     * @var array $parcelsInfo
     */
    private $parcelsInfo = [
        'weight' => '', 'length' => '', 'declaredPrice' => '',
    ];

    /**
     * @var array $shipmentInfo
     */
    private $shipmentInfo = [
        'paid_by_recipient' => '',
        'non_cash_payment'  => '',
        'sms'               => '',
        'check_on_delivery' => '',
    ];

    /**
     * @var UkrposhtaApiWrapper $wrapper
     */
    private $wrapper;

    /**
     * @var Orders $order
     */
    private $order;

    /**
     * UkrposhtaEnGenerator constructor.
     */
    public function __construct()
    {
        $this->simpla = new Simpla();

        $order_id    = $this->simpla->request->get('order_id', 'string');
        $this->order = $this->simpla->orders->get_order((int)$order_id);

        $this->senderInfo    = $this->getSenderInfo();
        $this->recipientInfo = $this->getRecipientInfo();
        $this->parcelsInfo   = $this->getParcelsInfo();
        $this->shipmentInfo  = $this->getShipmentInfo();

        $this->wrapper = new UkrposhtaApiWrapper(
            $this->simpla->settings->ukrposhta_bearer,
            $this->simpla->settings->ukrposhta_token);
    }

    /**
     * Generate a PDF document.
     * @return string json or pdf contents
     */
    public function generate()
    {
        try {
            // Create sender's address and client entities:
            $sender_address = $this->wrapper->address()
                                            ->create(['postcode' => $this->senderInfo['postcode']]);
            $sender_client  = $this->wrapper->client()
                                            ->create($this->createSenderEntity($sender_address->getId()));

            // Create recipient's address and client entities:
            $recipient_address = $this->wrapper->address()
                                               ->create(['postcode' => $this->recipientInfo['postcode']]);
            $recipient_client  = $this->wrapper->client()
                                               ->create($this->createRecipientEntity($recipient_address->getId()));

            // Create shipment:
            $shipment = $this->wrapper->shipment()
                                      ->create($this->createShipmentEntity($sender_client->getUuid(),
                                                                           $recipient_client->getUuid(), $this->parcelsInfo));

            // Print shipment (get contents of PDF)
            $pdf_contents = $this->wrapper->printForm()->shipmentSticker($shipment->getUuid());

            // Return created pdf without error.
            return json_encode(['pdf' => $this->createPdfFile($pdf_contents), 'error' => null,]);

        } catch (UkrposhtaApiException $exception) {
            return json_encode(['error' => [
                'message' => $exception->getMessage(),
                'code'    => $exception->getCode(),
            ],]);
        } catch
        (Exception $exc) {
            return json_encode([
                                   'error' => [
                                       'message' => $exc->getMessage(),
                                       'code'    => $exc->getCode(),
                                   ],
                               ]);
        }
    }

    /**
     * Create pdf file, fill it with pdf contents and return file path.
     *
     * @param string $pdfContents
     *
     * @return string url to file
     * @throws Exception
     */
    private function createPdfFile($pdfContents)
    {
        $filename = 'files/ukrpost/Накладная заказа №' . $this->order->id . '.pdf';
        $url      = $this->simpla->config->root_url . '/' . $filename;
        $filename = $this->simpla->config->root_dir . $filename;
        $dirname  = dirname($filename);

        if (!is_dir($dirname)) {
            mkdir($dirname, 0755, true);
        }

        $pdf = fopen($filename, 'w');

        if (!fwrite($pdf, $pdfContents)) {
            throw new Exception('PDF creation error');
        }

        fclose($pdf);

        return $url;
    }

    /**
     * Get recipient info form the order.
     * @return array
     */
    private function getRecipientInfo()
    {
        $recipient = [];

        $recipient['firstName']    = $this->simpla->request->get('recipient_name', 'string');
        $recipient['lastName']     = $this->simpla->request->get('recipient_sername', 'string');
        $recipient['postcode']     = $this->simpla->request->get('recipient_postcode', 'string');
        $recipient['bank_code']    = $this->simpla->request->get('recipient_bank_code', 'string');
        $recipient['bank_account'] = $this->simpla->request->get('recipient_bank_account', 'string');

        $recipient['phone'] = $this->order->phone;
        $recipient['email'] = $this->order->email;

        return $recipient;
    }

    /**
     * Get sender info form the settings.
     * @return array sender info
     */
    private function getSenderInfo()
    {
        $sender = [];

        $sender['phone']       = $this->simpla->settings->ukrposhta_sender_phone;
        $sender['postcode']    = $this->simpla->settings->ukrposhta_sender_postcode;
        $sender['type']        = $this->simpla->settings->ukrposhta_sender_type;
        $sender['first_name']  = $this->simpla->settings->ukrposhta_sender_first_name;
        $sender['last_name']   = $this->simpla->settings->ukrposhta_sender_last_name;
        $sender['name']        = $this->simpla->settings->ukrposhta_sender_name;
        $sender['tin']         = $this->simpla->settings->ukrposhta_sender_tin;
        $sender['middle_name'] = $this->simpla->settings->ukrposhta_sender_middle_name;
        $sender['edrpou']      = $this->simpla->settings->ukrposhta_sender_edrpou;

        return $sender;
    }

    /**
     * Get shipment info from get params.
     * @return array shipment info
     */
    private function getShipmentInfo()
    {
        $shipment = [];

        $shipment['paid_by_recipient'] = $this->simpla->request->get('paid_by', 'string') == 'recipient'
            ? true : false;
        $shipment['non_cash_payment']  = $this->simpla->request->get('payment_type', 'string') == 'noncash'
            ? true : false;
        $shipment['sms']               = $this->simpla->request->get('sms', 'boolean');
        $shipment['check_on_delivery'] = $this->simpla->request->get('check_on_delivery', 'boolean');

        return $shipment;
    }


    /**
     * Get parcels options for a new shipment:
     * @return array
     */
    private function getParcelsInfo()
    {
        $parcels['weight'] = empty($this->simpla->request->get('parcel_weight', 'string'))
            ? $this->order->ukrposhta_parcel_weight
            : $this->simpla->request->get('parcel_weight', 'string');

        $parcels['length'] = empty($this->simpla->request->get('parcel_length'))
            ? $this->order->ukrposhta_parcel_length
            : $this->simpla->request->get('parcel_length');


        $parcels['declaredPrice'] = $this->order->total_price;

        return $parcels;
    }

    /**
     * @param $addressId int
     *
     * @return Client
     */
    public function createRecipientEntity($addressId)
    {
        $recipient_client = new Client();

        $first_name = $this->recipientInfo['firstName'];
        $last_name  = $this->recipientInfo['lastName'];

        if ($first_name && $last_name) {
            $recipient_client->setFirstName($first_name)
                             ->setLastName($last_name)
                             ->setType(Client::TYPE_INDIVIDUAL);
        } else {
            $recipient_client->setName($this->recipientInfo['name'])
                             ->setType(Client::TYPE_PRIVATE_ENTREPRENEUR);
        }

        $recipient_client->setAddressId($addressId)
                         ->setEmail($this->recipientInfo['email'])
                         ->setPhoneNumber($this->recipientInfo['phone'])
                         ->setBankCode($this->recipientInfo['bank_code'])
                         ->setBankAccount($this->recipientInfo['bank_account']);

        return $recipient_client;
    }

    /**
     * Create sender client model using data from the settings.
     *
     * @param $addressId int
     *
     * @return Client
     */
    private function createSenderEntity($addressId)
    {
        $sender_client = new Client();

        if ($this->senderInfo['type'] == 'physical') {
            $sender_client->setFirstName($this->senderInfo['first_name'])
                          ->setLastName($this->senderInfo['last_name'])
                          ->setMiddleName($this->senderInfo['middle_name'])
                          ->setType(Client::TYPE_INDIVIDUAL);
        } else {
            if ($this->senderInfo['type'] == 'legal') {
                $sender_client->setName($this->senderInfo['name'])
                              ->setEdrpou($this->senderInfo['edrpou'])
                              ->setType(Client::TYPE_COMPANY);
            }
        }

        $sender_client->setPhoneNumber($this->senderInfo['phone'])
                      ->setAddressId($addressId)
                      ->setTin($this->senderInfo['tin']);

        return $sender_client;
    }

    /**
     * Create shipment entity.
     *
     * @param string $senderUuid
     * @param string $recipientUuid
     * @param array  $parcels weight(required), length(optional), declaredPrice(optional)
     * @param string $deliveryType
     *                        Тип доставки (4 основних типи:
     *                        W2D склад-двері, W2W склад-склад , D2W двері-склад, D2D двері- двері)
     *                        (Для Укрпошта STANDART(type:STANDA RT) тільки з оголошеною цінністю)
     *
     * @return Shipment
     */
    private function createShipmentEntity($senderUuid, $recipientUuid, $parcels, $deliveryType = 'W2W')
    {
        return new Shipment([
                                'sender'          => ['uuid' => $senderUuid],
                                'recipient'       => ['uuid' => $recipientUuid],
                                'deliveryType'    => $deliveryType,
                                'paidByRecipient' => $this->shipmentInfo['paid_by_recipient'],
                                'nonCashPayment'  => $this->shipmentInfo['non_cash_payment'],
                                'sms'             => $this->shipmentInfo['sms'],
                                'postPay'         => $parcels['declaredPrice'],
                                'checkOnDelivery' => $this->shipmentInfo['check_on_delivery'],
                                'parcels'         => [[
                                    'weight'        => $parcels['weight'],
                                    'length'        => $parcels['length'],
                                    'declaredPrice' => $parcels['declaredPrice']],
                                ],
                            ]);
    }
}

$obj    = new UkrposhtaEnGenerator();
$result = $obj->generate();
echo $result;