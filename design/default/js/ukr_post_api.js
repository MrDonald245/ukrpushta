$(function () {
    $('[name=delivery_id]').change(function () {
        if ($(this).val() === "777777") {
            $('#block_ukr_post').show(300);
        } else {
            $('#block_ukr_post').hide(300);
        }
    });

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
                recipient_inn:          $('#ukrposhta_recipient_inn').val(),
                recipient_bank_code:    $('#ukrposhta_recipient_bank_code:not(:disabled)').val(),
                recipient_bank_account: $('#ukrposhta_recipient_bank_account:not(:disabled)').val(),
                parcel_weight:          $('#ukrposhta_parcel_weight').val(),
                parcel_length:          $('#ukrposhta_parcel_length').val(),
                paid_by:                $('[name=ukrposhta_paid_by]:checked').val(),
                payment_type:           $('[name=ukrposhta_payment_type]:checked').val(),
                sms:                    $('#ukrposhta_sms').is(':checked'),
                check_on_delivery:      $('#ukrposhta_check_on_delivery').is(':checked')
            },
            dataType: 'json',
            success:  function (data) {
                if (data.error) {
                    $.fancybox({content: '<h2>' + data.error.message + '</h2>'});
                    console.error(data.error.message);
                } else {
                    var test = $("#test");
                    test.attr('href', data.pdf);
                    test.fancybox({
                        width:  600,
                        height: 300,
                        type:   'iframe'
                    });
                    test.click();
                }

            },
            error:    function (requestObject, error, errorThrown) {
                $.fancybox({content: errorThrown.message});
                console.error(errorThrown.message);
            }
        });
    });

    // When detailed info link is pressed:
    var detailedInfoLinks = $("#ukrpost_detailed_info a").click(function () {
        // Switch detailed info button:
        detailedInfoLinks.not(":visible").show('slow');
        $(this).hide();

        // Switch visibility of detailed info body block:
        var detailedInfoBlock = $("#ukrpost_detailed_info_body");
        detailedInfoBlock.is(":visible")
            ? detailedInfoBlock.hide('slow')
            : detailedInfoBlock.show('slow');
    });
});