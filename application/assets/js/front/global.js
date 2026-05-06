jQuery(document).ready(function () {
    jQuery('body')
        .on('click', '#link-menu-mobile', function () {
            if (jQuery('#menu').css('height') === '0px') {
                jQuery('#menu').animateAutoMenu('height', 500);
            } else {
                jQuery('#menu').animate({
                    height: '0px'
                }, 500);

                if (jQuery('#page-home').length === 0) {
                    jQuery('body').animate({
                        backgroundPositionY: '-580px'
                    }, 500);
                }
            }
        })
        .on('click', '.modal-table-offres .bouton button', function () {
            let alreadyActive = jQuery(this).hasClass('active');
            jQuery('.modal-table-offres .bouton button').removeClass('active');
            if (alreadyActive)
                jQuery('#mjmt_front_inscription_project_offre_1').prop('checked', true);
            else
                jQuery(this).addClass('active').closest('.une-offre').find('.radio input[type="radio"]').prop('checked', true);

            // jQuery(this).closest('.modal-offre').modal('hide');

            return false;
        });

    jQuery('#header-menu-mobile').on('click', function () {
        if (!jQuery('#menu-content-mobile').is(':visible')) {
            jQuery('.fixedGrassHeader').css('background', 'url(/front/images/header-herbe-haut-mobile.jpg)');
            jQuery('.fixedGrassHeader').css('background-size', 'cover');
            jQuery('.fixedGrassOuterBorder').css('background-size', 'cover');
            jQuery('#menu-content-mobile').show();
        } else {
            jQuery('.fixedGrassHeader').css('background', 'url(/front/images/header-herbe-haut.jpg)');
            jQuery('.fixedGrassHeader').css('background-size', 'contain');
            jQuery('.fixedGrassOuterBorder').css('background-size', 'contain');
            jQuery('#menu-content-mobile').hide();
        }
    })
});

jQuery.fn.animateAutoMenu = function (prop, speed, callback) {
    var elem, height, width;
    return this.each(function (i, el) {
        el = jQuery(el), elem = el.clone().css({"height": "auto", "width": "auto"}).appendTo("body");
        height = elem.css("height"),
            width = elem.css("width"),
            elem.remove();

        if (prop === "height") {
            el.animate({"height": height}, speed, callback);
            if (jQuery('#accueil').length === 0) {
                jQuery('body').animate({
                    backgroundPositionY: parseFloat(jQuery('body').css('background-position-y')) + parseFloat(height)
                }, 500);
            }
        } else if (prop === "width") {
            el.animate({"width": width}, speed, callback);
        } else if (prop === "both") {
            el.animate({"width": width, "height": height}, speed, callback);
        }
    });
}