$(function () {
    var senderTypeRadioInputs = $('#ukrposhta_sender_type_block').children('input'),
        checkedInput          = senderTypeRadioInputs.filter(':checked');

    if (checkedInput.length === 0) {
        senderTypeRadioInputs.first().attr('checked', true);
    }

    var ukrposhtaPhysicalBlock = $('#ukrposhta_physical_block'),
        ukrposhtaLegalBlock    = $('#ukrposhta_legal_block');

    if (checkedInput.val() === 'physical') {
        ukrposhtaPhysicalBlock.show(200);
    } else if (checkedInput.val() === 'legal') {
        ukrposhtaLegalBlock.show(200);
    }

    senderTypeRadioInputs.change(function () {
        if (this.checked) {
            switch ($(this).val()) {
                case 'physical':
                    ukrposhtaLegalBlock.hide(200);
                    ukrposhtaPhysicalBlock.show(200);
                    break;
                case 'legal':
                    ukrposhtaPhysicalBlock.hide(200);
                    ukrposhtaLegalBlock.show(200);
                    break;
            }
        }
    });
});