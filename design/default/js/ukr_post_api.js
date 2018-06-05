$(function () {
    $('[name=delivery_id]').change(function () {
        if ($(this).val() === "777") {
            $('#block_ukr_post').show(300);
        } else {
            $('#block_ukr_post').hide(300);
        }
    }); // $('[name=delivery_id]').change()

    // When Ukrpost print order button is pressed:
    $('.ukrposhta_add_en').click(function () {
        var recipient_postcode = $('#ukrposhta_recipient_postcode').val();
        if (recipient_postcode === '') {
            $.fancybox({
                content: '<br/><div><h2>Не заполнен почтовый индекс получателя.</h2><br/><div>',
                helpers: {overlay: {locked: false}}
            });
            return false;
        }

        $.fancybox({content: '<br/><div><h2>Документ создается</h2><br/><div>', helpers: {overlay: {locked: false}}});

        $.ajax({
            url:      "/simpla/ajax/ukr_post_generate_en.php",
            data:     {
                order_id:               $('.ukrposhta_add_en').attr('id_orders'),
                recipient_postcode:     recipient_postcode,
                recipient_name:         $('#ukrposhta_recipient_name').val(),
                recipient_sername:      $('#ukrposhta_recipient_sername').val(),
                recipient_bank_code:    $('#ukrposhta_recipient_bank_code:not(:disabled)').val(),
                recipient_bank_account: $('#ukrposhta_recipient_bank_account:not(:disabled)').val(),
                parcel_weight:          $('#ukrposhta_parcel_weight').val(),
                post_pay:               $('#ukrposhta_post_pay').is(':checked'),
                paid_by:                $('[name=ukrposhta_paid_by]:checked').val()
            },
            dataType: 'json',
            success:  function (data) {
                if (data.error) {
                    $.fancybox({content: '<h2>' + data.error.message + '</h2>'});
                    console.error(data.error.message);
                } else {
                    $('form#order').submit();
                }

            },
            error:    function (requestObject, error, errorThrown) {
                $.fancybox({content: errorThrown.message});
                console.error(errorThrown.message);
            }
        });
    }); // $('.ukrposhta_add_en').click()

    $('a.shipment-link').click(function (event) {
        event.preventDefault();
        $(this).fancybox({
            width:  600,
            height: 300,
            type:   'iframe'
        });
    }); // $('a.shipment-link').click()
});