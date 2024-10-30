jQuery(document).ready(function() {
    jQuery('[name=cs_ar_config_use_custom_post_type]').on('change', function(){
        if(jQuery('[name=cs_ar_config_use_custom_post_type]:checked').val() == '1') {
            jQuery('#cs_ar_appearance_settings').removeClass('cs_ar_no_show');
        } else if(jQuery('[name=cs_ar_config_use_custom_post_type]:checked').val() == '0') {
            jQuery('#cs_ar_appearance_settings').addClass('cs_ar_no_show');
        }
    });
});