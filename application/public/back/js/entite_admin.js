jQuery(document).ready(function () {
    changeForm(jQuery('select[name$="[type]"]').val());

    jQuery('.control-group select[name$="[type]"]').change(function () {
        changeForm(jQuery(this).val());
    });
});

function changeForm(type) {

    jQuery('.entite-' + type).closest('.control-group').show();
    jQuery('.entite-' + (type == 'objet' ? 'plante' : 'objet')).closest('.control-group').hide();

    jQuery('form .tab-pane').each(function () {
        var isVisible = false;

        jQuery(this).find('.control-group').each(function () {
            if (jQuery(this).css('display') != 'none')
                isVisible = true;
        });

        if (!isVisible) {
            jQuery(this).css('display', 'none');
            jQuery('.nav-tabs a[href="#' + jQuery(this).prop('id') + '"]').css('display', 'none');
        } else {
            jQuery(this).removeProp('style');
            jQuery('.nav-tabs a[href="#' + jQuery(this).prop('id') + '"]').removeProp('style');
        }
    });
}