{capture name=tabs}
  <li class="active"><a href="index.php?module=SettingsAdmin">Настройки</a></li>
  {if in_array('currency', $manager->permissions)}
    <li><a href="index.php?module=CurrencyAdmin">Валюты</a></li>
  {/if}
  {if in_array('delivery', $manager->permissions)}
    <li><a href="index.php?module=DeliveriesAdmin">Доставка</a></li>
  {/if}
  {if in_array('payment', $manager->permissions)}
    <li><a href="index.php?module=PaymentMethodsAdmin">Оплата</a></li>
  {/if}
  {if in_array('managers', $manager->permissions)}
    <li><a href="index.php?module=ManagersAdmin">Менеджеры</a></li>
  {/if}
{/capture}

{$meta_title = "Настройки" scope=parent}

{if $message_success}
  <!-- Системное сообщение -->
  <div class="message message_success">
    <span class="text">{if $message_success == 'saved'}Настройки сохранены{/if}</span>
    {if $smarty.get.return}
      <a class="button" href="{$smarty.get.return}">Вернуться</a>
    {/if}
  </div>
  <!-- Системное сообщение (The End)-->
{/if}

{if $message_error}
  <!-- Системное сообщение -->
  <div class="message message_error">
    <span class="text">{if $message_error == 'watermark_is_not_writable'}Установите права на запись для файла {$config->watermark_file}{/if}</span>
    <a class="button" href="">Вернуться</a>
  </div>
  <!-- Системное сообщение (The End)-->
{/if}


<!-- Основная форма -->
<form method=post id=product enctype="multipart/form-data">
  <input type=hidden name="session_id" value="{$smarty.session.id}">

  <!-- Параметры -->
  <div class="block">
    <h2>Настройки сайта</h2>
    <ul>
      <li><label class=property>Имя сайта</label><input name="site_name" class="simpla_inp" type="text"
                                                        value="{$settings->site_name|escape}"/></li>
      <li><label class=property>Имя компании</label><input name="company_name" class="simpla_inp" type="text"
                                                           value="{$settings->company_name|escape}"/></li>
      <li><label class=property>Формат даты</label><input name="date_format" class="simpla_inp" type="text"
                                                          value="{$settings->date_format|escape}"/></li>
      <li><label class=property>Email для восстановления пароля</label><input name="admin_email" class="simpla_inp"
                                                                              type="text"
                                                                              value="{$settings->admin_email|escape}"/>
      </li>
    </ul>
  </div>
  <div class="block layer">
    <h2>Оповещения</h2>
    <ul>
      <li><label class=property>Оповещение о заказах</label><input name="order_email" class="simpla_inp" type="text"
                                                                   value="{$settings->order_email|escape}"/></li>
      <li><label class=property>Оповещение о комментариях</label><input name="comment_email" class="simpla_inp"
                                                                        type="text"
                                                                        value="{$settings->comment_email|escape}"/></li>
      <li><label class=property>Обратный адрес оповещений</label><input name="notify_from_email" class="simpla_inp"
                                                                        type="text"
                                                                        value="{$settings->notify_from_email|escape}"/>
      </li>
    </ul>
  </div>
  <!-- Параметры (The End)-->

  <!-- Параметры -->
  <div class="block layer">
    <h2>Формат цены</h2>
    <ul>
      <li><label class=property>Разделитель копеек</label>
        <select name="decimals_point" class="simpla_inp">
          <option value='.' {if $settings->decimals_point == '.'}selected{/if}>точка:
            12.45 {$currency->sign|escape}</option>
          <option value=',' {if $settings->decimals_point == ','}selected{/if}>запятая:
            12,45 {$currency->sign|escape}</option>
        </select>
      </li>
      <li><label class=property>Разделитель тысяч</label>
        <select name="thousands_separator" class="simpla_inp">
          <option value='' {if $settings->thousands_separator == ''}selected{/if}>без разделителя:
            1245678 {$currency->sign|escape}</option>
          <option value=' ' {if $settings->thousands_separator == ' '}selected{/if}>пробел: 1 245
            678 {$currency->sign|escape}</option>
          <option value=',' {if $settings->thousands_separator == ','}selected{/if}>запятая:
            1,245,678 {$currency->sign|escape}</option>
        </select>


      </li>
    </ul>
  </div>
  <!-- Параметры (The End)-->

  <!-- Параметры -->
  <div class="block layer">
    <h2>Настройки каталога</h2>
    <ul>
      <li><label class=property>Товаров на странице сайта</label><input name="products_num" class="simpla_inp"
                                                                        type="text"
                                                                        value="{$settings->products_num|escape}"/></li>
      <li><label class=property>Товаров на странице админки</label><input name="products_num_admin" class="simpla_inp"
                                                                          type="text"
                                                                          value="{$settings->products_num_admin|escape}"/>
      </li>
      <li><label class=property>Максимум товаров в заказе</label><input name="max_order_amount" class="simpla_inp"
                                                                        type="text"
                                                                        value="{$settings->max_order_amount|escape}"/>
      </li>
      <li><label class=property>Единицы измерения товаров</label><input name="units" class="simpla_inp" type="text"
                                                                        value="{$settings->units|escape}"/></li>
    </ul>
  </div>
  <!-- Параметры (The End)-->

  <!-- Параметры -->
  <div class="block layer">
    <h2>Изображения товаров</h2>

    <ul>
      <li><label class=property>Водяной знак</label>
        <input name="watermark_file" class="simpla_inp" type="file"/>

        <img style='display:block; border:1px solid #d0d0d0; margin:10px 0 10px 0;'
             src="{$config->root_url}/{$config->watermark_file}?{math equation='rand(10,10000)'}">
      </li>
      <li><label class=property>Горизонтальное положение водяного знака</label><input name="watermark_offset_x"
                                                                                      class="simpla_inp" type="text"
                                                                                      value="{$settings->watermark_offset_x|escape}"/>
        %
      </li>
      <li><label class=property>Вертикальное положение водяного знака</label><input name="watermark_offset_y"
                                                                                    class="simpla_inp" type="text"
                                                                                    value="{$settings->watermark_offset_y|escape}"/>
        %
      </li>
      <li><label class=property>Прозрачность знака (больше &mdash; прозрачней)</label><input
                name="watermark_transparency" class="simpla_inp" type="text"
                value="{$settings->watermark_transparency|escape}"/> %
      </li>
      <li><label class=property>Резкость изображений (рекомендуется 20%)</label><input name="images_sharpen"
                                                                                       class="simpla_inp" type="text"
                                                                                       value="{$settings->images_sharpen|escape}"/>
        %
      </li>
    </ul>
  </div>
  <!-- Параметры (The End)-->


  <!-- Параметры -->
  <div class="block layer">
    <h2>Интеграция с <a href="http://prostiezvonki.ru">простыми звонками</a></h2>
    <ul>
      <li><label class=property>Сервер</label><input name="pz_server" class="simpla_inp" type="text"
                                                     value="{$settings->pz_server|escape}"/></li>
      <li><label class=property>Пароль</label><input name="pz_password" class="simpla_inp" type="text"
                                                     value="{$settings->pz_password|escape}"/></li>
      <li><label class=property>Телефоны менеджеров:</label></li>
      {foreach $managers as $manager}
        <li><label class=property>{$manager->login}</label><input name="pz_phones[{$manager->login}]" class="simpla_inp"
                                                                  type="text"
                                                                  value="{$settings->pz_phones[$manager->login]|escape}"/>
        </li>
      {/foreach}
    </ul>
  </div>
  <!-- Параметры (The End)-->

  {* ukrposhta *}
  <div class="block layer">
    <h2>Настройки "Укрпочта"</h2>
    <ul>
      <li><label for="ukrposhta_token" class=property>Token</label>
        <input name="ukrposhta_token" id="ukrposhta_token" class="simpla_inp" type="text"
               value="{$settings->ukrposhta_token|escape}"/></li>
      <li><label for="ukrposhta_bearer" class=property>Bearer</label>
        <input name="ukrposhta_bearer" id="ukrposhta_bearer" class="simpla_inp" type="text"
               value="{$settings->ukrposhta_bearer|escape}"/></li>
      <li><label for="ukrposhta_sender_postcode" class=property>Почтовый индекс отправителя</label>
        <input name="ukrposhta_sender_postcode" id="ukrposhta_sender_postcode" class="simpla_inp"
               type="text" value="{$settings->ukrposhta_sender_postcode|escape}"/></li>
      <li><label for="ukrposhta_sender_phone" class=property>Номер телефона отправителя</label>
        <input name="ukrposhta_sender_phone" id="ukrposhta_sender_phone" class="simpla_inp"
               type="text" value="{$settings->ukrposhta_sender_phone|escape}"/></li>
      <li id="ukrposhta_sender_type_block"><label class=property>Вид субъекта</label>
        <input id="ukrposhta_sender_physical" name="ukrposhta_sender_type" class="simpla_inp" type="radio"
               value="physical"
               {if $settings->ukrposhta_sender_type=='physical'}checked="checked"{/if}/><label
                for="ukrposhta_sender_physical">Физлицо</label>
        <input name="ukrposhta_sender_type" id="ukrposhta_sender_legal" class="simpla_inp" type="radio"
               value="legal"
               {if $settings->ukrposhta_sender_type=='legal'}checked="checked"{/if}/><label
                for="ukrposhta_sender_legal">Юрлицо</label>
      </li>
      <div id="ukrposhta_physical_block" style="display: none">
        <li><label for="ukrposhta_sender_first_name" class=property>Имя</label>
          <input name="ukrposhta_sender_first_name" id="ukrposhta_sender_first_name" class="simpla_inp"
                 type="text"
                 value="{$settings->ukrposhta_sender_first_name|escape}"/></li>
        <li><label for="ukrposhta_sender_last_name" class=property>Фамилия</label>
          <input name="ukrposhta_sender_last_name" id="ukrposhta_sender_last_name" class="simpla_inp"
                 type="text"
                 value="{$settings->ukrposhta_sender_last_name|escape}"/></li>
        <li><label for="ukrposhta_sender_middle_name" class=property>Отчество</label>
          <input name="ukrposhta_sender_middle_name" id="ukrposhta_sender_middle_name" class="simpla_inp"
                 type="text"
                 value="{$settings->ukrposhta_sender_middle_name|escape}"/></li>
      </div>
      <div id="ukrposhta_legal_block">
        <li><label for="ukrposhta_sender_name" class=property>Название предприятия</label>
          <input name="ukrposhta_sender_name" id="ukrposhta_sender_name" class="simpla_inp"
                 type="text"
                 value="{$settings->ukrposhta_sender_name|escape}"></li>

        <li><label for="ukrposhta_sender_edrpou" class=property>Код по ЄДРПОУ</label>
          <input name="ukrposhta_sender_edrpou" id="ukrposhta_sender_edrpou" class="simpla_inp"
                 type="text"
                 value="{$settings->ukrposhta_sender_edrpou|escape}"></li>
      </div>

      <li><label for="ukrposhta_parcel_length" class="property">Размер самой большой стороны посылки(см)</label>
        <input name="ukrposhta_parcel_length" id="ukrposhta_parcel_length"
               type="text" {if $settings->ukrposhta_parcel_length} value="{$settings->ukrposhta_parcel_length}"{/if}>
      </li>

      <li><label for="ukrposhta_sms" class="property">Отправлять SMS получателю</label>
        <input id="ukrposhta_sms" name="ukrposhta_sms" type="checkbox" {if $settings->ukrposhta_sms}checked{/if}></li>

      <li><label for="ukrposhta_check_on_delivery" class="property">Разрешить проверку посылки</label>
        <input id="ukrposhta_check_on_delivery" name="ukrposhta_check_on_delivery" type="checkbox"
               {if $settings->ukrposhta_check_on_delivery}checked{/if}></li>

      <li><label for="ukrposhta_noncash_payment" class="property">Безналичный расчет</label>
        <input name="ukrposhta_noncash_payment" id="ukrposhta_noncash_payment" type="checkbox"
               {if $settings->ukrposhta_noncash_payment}checked{/if}/>
      </li>
      {* /ukrposhta *}

      <input class="button_green button_save" type="submit" name="save" value="Сохранить"/>

      <!-- Левая колонка свойств товара (The End)-->

</form>
<!-- Основная форма (The End) -->
{* ukrposhta *}
<script src="/simpla/design/js/ukr_post_api.js"></script>
{* /ukrposhta *}