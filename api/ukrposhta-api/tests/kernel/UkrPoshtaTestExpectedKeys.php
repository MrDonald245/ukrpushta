<?php
/**
 * Created by Eugene.
 * User: eugene
 * Date: 03/04/18
 * Time: 12:25
 */

/**
 * Class UkrPoshtaTestExpectedKeys is used by UkrPoshtaApiTest
 * in order to check keys for fetched arrays.
 */
class UkrPoshtaTestExpectedKeys
{
    const ADDRESS_VALID_KEYS = [
        'id', 'postcode', 'region', 'district', 'city', 'street', 'houseNumber',
        'apartmentNumber', 'description', 'countryside', 'foreignStreetHouseApartment',
        'detailedInfo', 'country',
    ];

    const CLIENT_VALID_KEYS = [
        'uuid', 'name', 'firstName', 'middleName', 'lastName', 'nameEn', 'firstNameEn',
        'lastNameEn', 'postId', 'externalId', 'uniqueRegistrationNumber', 'counterpartyUuid',
        'addressId', 'addresses', 'phoneNumber', 'phones', 'email', 'emails', 'type',
        'individual', 'edrpou', 'bankCode', 'bankAccount', 'tin', 'contactPersonName',
        'resident',
    ];

    const SHIPMENT_GROUP_VALID_KEYS = [
        'uuid', 'name', 'type', 'clientUuid', 'closed',
    ];

    const SHIPMENT_VALID_KEYS = [
        'uuid', 'type', 'sender', 'dropOffPostcode', 'recipient', 'recipientPhone',
        'recipientEmail', 'recipientAddressId', 'returnAddressId', 'shipmentGroupUuid',
        'externalId', 'deliveryType', 'packageType', 'onFailReceiveType', 'barcode',
        'weight', 'length', 'width', 'height', 'declaredPrice', 'deliveryPrice',
        'postPay', 'postPayUah', 'postPayDeliveryPrice', 'currencyCode', 'postPayCurrencyCode',
        'currencyExchangeRate', 'discount', 'lastModified', 'description', 'parcels',
        'direction', 'lifecycle', 'deliveryDate', 'calculationDescription', 'international',
        'paidByRecipient', 'postPayPaidByRecipient', 'nonCashPayment', 'bulky', 'fragile',
        'bees', 'recommended', 'sms', 'toReturnToSender', 'documentBack', 'checkOnDelivery',
        'transferPostPayToBankAccount', 'deliveryPricePaid', 'postPayPaid', 'postPayDeliveryPricePaid',
        'packedBySender', 'free', 'discountPerClient',
    ];
}