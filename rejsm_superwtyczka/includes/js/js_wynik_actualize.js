jQuery(document).ready(function () {

    function checkHealth() {
        var total = 0;
        jQuery.each(jQuery('.msis_check'), function () {
            if (jQuery(this).prop('checked') === true) {
                total = total + Number(jQuery(this).val());
            }
        });
        jQuery('#health_status_msis').val(total);
    }
    function EuremsInputsCheck() {
        if (jQuery('#mri_yes').prop('checked') === false) {
            jQuery('#mri_date').prop('disabled', true);
            jQuery('#mri_date').attr('disabled', 'disabled');
        }
        else {
            jQuery('#mri_date').prop('disabled', false);
            jQuery('#mri_date').removeAttr('disabled');
        }

        if (jQuery('#cereb_yes').prop('checked') === false) {
            jQuery('#potenc_result').prop('disabled', true);
            jQuery('#potenc_result').attr('disabled', 'disabled');
        }
        else {
            jQuery('#potenc_result').prop('disabled', false);
            jQuery('#potenc_result').removeAttr('disabled');
        }

        if (jQuery('#work_yes').prop('checked') === false) {
            jQuery('#work_mon_yes').prop('disabled', true);
            jQuery('#work_mon_yes').attr('disabled', 'disabled');
            jQuery('#work_mon_no').prop('disabled', true);
            jQuery('#work_mon_no').attr('disabled', 'disabled');
        }
        else {
            jQuery('#work_mon_yes').prop('disabled', false);
            jQuery('#work_mon_yes').removeAttr('disabled');
            jQuery('#work_mon_no').prop('disabled', false);
            jQuery('#work_mon_no').removeAttr('disabled');
        }
    }
    // na początek blokowanie inputów i liczenie stanu zdrowia  
    checkHealth();
    EuremsInputsCheck();

    jQuery('.msis_check').on('click', function () {
        checkHealth();
    });

    jQuery('.eurems_input_check').on('change', function () {
        EuremsInputsCheck();
    });

    jQuery('#edss option').on('click', function () {
        if (jQuery(this).val() === '') {
            jQuery('#edss_date').prop('disabled', true);
            jQuery('#edss_date').attr('disabled', 'disabled');
        }
        else {
            jQuery('#edss_date').prop('disabled', false);
            jQuery('#edss_date').removeAttr('disabled');
        }
    });

    jQuery('div.images img').on('mouseover', function () {
        if (jQuery(this).hasClass('confirmed-image') === false) {
            jQuery(this).attr('src', './resources/kreska_czerw.png');
            jQuery(this).addClass('selected-image');
        }
    });

    jQuery('div.images img').on('mouseout', function () {
        if (jQuery(this).hasClass('confirmed-image') === false) {
            jQuery(this).attr('src', jQuery(this).attr('rel'));
            jQuery(this).removeClass('selected-image');
        }
    });

    jQuery('div.images img').on('click', function () {

        jQuery.each(jQuery('div.images img'), function (index, value) {
            jQuery(value).removeClass('confirmed-image');
            jQuery(value).removeClass('selected-image');
            jQuery(value).attr('src', jQuery(value).attr('rel'));
        });

        jQuery(this).attr('src', './resources/kreska_czerw.png');
        jQuery(this).addClass('confirmed-image');
        var result = jQuery(this).attr('alt');
        jQuery('input.scale_result').val(result);
    });

    jQuery('#health_status').on('keyup', function () {
        var value = jQuery(this).val();
        console.log(jQuery('div.images').find('img[alt="' + value + '"]').length > 0);
        if (jQuery('div.images').find('img[alt="' + value + '"]').length > 0) {
            jQuery('div.images img').each(function (index, value) {
                jQuery(value).removeClass('confirmed-image');
                jQuery(value).attr('src', jQuery(value).attr('rel'));
            });

            jQuery('div.images img[alt="' + value + '"]').attr('src', './resources/kreska_czerw.png');
            jQuery('div.images img[alt="' + value + '"]').addClass('confirmed-image');
        }
    });
});