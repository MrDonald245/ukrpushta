create table s_ukrposhta_order
(
  id                     int auto_increment primary key,
  order_id               int                                             null,
  recipient_name         varchar(60)                                     null,
  recipient_sername      varchar(60)                                     null,
  recipient_postcode     varchar(16)                                     null,
  recipient_bank_code    varchar(60)                                     null,
  recipient_bank_account varchar(60)                                     null,
  parcel_weight          int                           default '0'       null,
  parcel_length          int                           default '0'       null,
  paid_by                enum ('sender',  'recipient') default 'sender'  null,
  payment_type           enum ('noncash', 'cash')      default 'noncash' null,
  sms                    tinyint(1)                    default '0'       null,
  check_on_delivery      tinyint(1)                    default '0'       null,
  post_pay               tinyint(1)                    default '1'       null,
);

INSERT INTO s_delivery (id, name, description, enabled, position)
    VALUES (777, 'Укрпочта', '', 1 , 777);