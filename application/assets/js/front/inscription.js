import {transObject} from '../translations/translation';

var geocoder;
var addresspickerMap;

jQuery(document).ready(function () {
    geocoder = new google.maps.Geocoder();

    if (jQuery('#page-inscription-client').length !== 0) {
        jQuery('body')
            .on('click', '.button-inscription-next', function (e) {
                e.preventDefault();

                let promoCode = jQuery('#summary-promo-code').val();

                if (jQuery('#choice-only-promo-content').length > 0) {
                    jQuery('#promo_code_codePromo').val(promoCode);

                    jQuery('#choice-only-promo-content form input[type="submit"]').trigger('click');
                } else {
                    if (!jQuery('#choice-register-content').hasClass('d-none')) {
                        jQuery('#inscription_client_common_codePromo_codePromo').val(promoCode);
                        jQuery('#client_connection_common_codePromo_codePromo').val('');

                        jQuery('#choice-register-content form input[type="submit"]').trigger('click');
                    } else if (!jQuery('#choice-connexion-content').hasClass('d-none')) {
                        jQuery('#inscription_client_common_codePromo_codePromo').val('');
                        jQuery('#client_connection_common_codePromo_codePromo').val(promoCode);

                        jQuery('#choice-connexion-content form input[type="submit"]').trigger('click');
                    } else {
                        jQuery('#inscription_client_common_codePromo_codePromo').val('');
                        jQuery('#client_connection_common_codePromo_codePromo').val('');
                    }
                }
            })
            .on('click', '.choice-register-connexion', function (e) {
                e.preventDefault();

                if (!jQuery(this).hasClass('active')) {
                    jQuery('.choice-register-connexion').removeClass('active');
                    jQuery(this).addClass('active');
                }

                if (jQuery(this).attr('data-value') === 'register') {
                    jQuery('#choice-connexion-content').addClass('d-none');
                    jQuery('#choice-register-content').removeClass('d-none');
                } else {
                    jQuery('#choice-connexion-content').removeClass('d-none');
                    jQuery('#choice-register-content').addClass('d-none');
                }
            })
    }

    if (jQuery('#page-inscription').length !== 0) {
        jQuery('body')
            .on('click', '#boutton-voir-offres', function (e) {
                e.preventDefault();

                if (jQuery('#mjmt_front_inscription_project_adresse').val() === '') {
                    jQuery('#mjmt_front_inscription_project_adresse').addClass('address-error-manual');
                    jQuery('.address-error-manual-text').removeClass('d-none').addClass('d-block');
                    jQuery('html, body').animate({scrollTop: 0}, 'slow');
                    return false;
                } else {
                    jQuery('#mjmt_front_inscription_project_adresse').removeClass('address-error-manual');
                    jQuery('.address-error-manual-text').removeClass('d-block').addClass('d-none');
                }
            })
            .on('click', '.btn-more-details', function () {
                if (jQuery('.ferme-details').css('display') === 'none') {
                    jQuery('.ferme-details').slideDown();
                    jQuery(this).html('Moins de détails');
                } else {
                    jQuery('.ferme-details').slideUp();
                    jQuery(this).html('Plus de détails');
                }
                return false;
            })
            .on('click', '#mjmt_frontbundle_projet_inscription_one_page_photo_banque-photo label', function () {
                updateSelectedPhoto();
            })
            .on('click', '.update-offer-choice', function () {
                updateOfferChoice();
            })
            .on('click', 'input[name="mjmt_front_inscription_project[projetType]"]', function () {
                changeProjectType();
            })
            .on('change', '#mjmt_front_inscription_project_banquePhoto_file', function () {
                if (jQuery(this).val() !== '') {
                    jQuery('#mjmt_front_inscription_project_banquePhotos_placeholder').prop('checked', true);
                    jQuery('.tick-choix-ok').remove();
                }
            });

        jQuery('.modal-offre')
            .on('hidden.bs.modal', function (event) {
                updateOfferChoice();
            });

        updateOfferChoice();
        loadPhoto();
        changeProjectType();
        addressInitialize();
    }
});

//Get the latitude and the longitude;
function addressSuccessFunction(position) {
    var lat = position.coords.latitude;
    var lng = position.coords.longitude;
    addressCodeLatLng(lat, lng)
}

function addressErrorFunction() {
    alert('Attention ! Géolocalisation impossible. Il se peut que celle-ci soit désactivée dans vos options.');
}

function addressInitialize() {
    var locale = jQuery('html').attr('lang').trim();

    if (jQuery('#mjmt_front_inscription_project_adresse').length > 0) {
        let iso = ['FR', 'BE', 'GB', 'IM', 'IE', 'IS', 'FO', 'NO', 'SE', 'FI', 'AX', 'EE', 'LV', 'BY', 'LT', 'PL', 'DK', 'UA', 'DE', 'NL', 'LU', 'CZ', 'SK', 'CH', 'LI', 'AT', 'HU', 'RO', 'MD', 'AD', 'ES', 'PT', 'IT', 'TR', 'GR', 'ME', 'MK', 'BG', 'RS', 'BA', 'HR', 'SI', 'MC', 'SM', 'VA', 'GI', 'TF', 'MA', 'TN', 'DZ'];
        let componentsFilter = '';
        iso.forEach(v => {
            componentsFilter += (componentsFilter != '' ? '|' : '') + 'country:' + v;
        });

        if (locale == 'fr') {
            addresspickerMap = jQuery("#mjmt_front_inscription_project_adresse").addresspicker({
                regionBias: "fr",
                componentsFilter: componentsFilter,
                elements: {
                    lat: "#mjmt_front_inscription_project_latitude",
                    lng: "#mjmt_front_inscription_project_longitude"
                }
            });
        } else {
            addresspickerMap = jQuery("#mjmt_front_inscription_project_adresse").addresspicker({
                // componentsFilter: componentsFilter,
                elements: {
                    lat: "#mjmt_front_inscription_project_latitude",
                    lng: "#mjmt_front_inscription_project_longitude"
                }
            });
        }

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(addressSuccessFunction, addressErrorFunction);
        }
    }
}

function addressCodeLatLng(lat, lng) {
    jQuery("#mjmt_front_inscription_project_latitude").val(lat);
    jQuery("#mjmt_front_inscription_project_longitude").val(lng);


    var latlng = new google.maps.LatLng(lat, lng);
    geocoder.geocode({'latLng': latlng}, function (results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            if (results[1]) {
                jQuery('#mjmt_front_inscription_project_adresse').val(results[0].formatted_address);
            } else {
                alert('Attention, aucune adresse trouvée par géolocalisation.');
            }
        } else {
            alert('Problème de géolocalisation.');
        }
    });
}

function updateOfferChoice() {
    var selectedOfferDiv = jQuery('input[name="mjmt_front_inscription_project[offre]"]:checked').closest('.offre-detail');

    var price = selectedOfferDiv.attr('data-prix');
    var photos = selectedOfferDiv.attr('data-photo');
    var objects = selectedOfferDiv.attr('data-objet');
    var grounds = selectedOfferDiv.attr('data-paysagiste');

    jQuery('#affichage-prix').html(jQuery.trim(price));
    jQuery('#afichage-nb-photos-personnelles').html(jQuery.trim(photos + (photos > 1 ? ' photos' : ' photo')));
    jQuery('#afichage-nb-objets').html(jQuery.trim(objects) === transObject.trans('limité') ? transObject.trans('Limité à 5 objets') : transObject.trans('Illimité'));
    jQuery('#afichage-aide-souhaite').html(jQuery.trim(grounds ? transObject.trans('Oui') : transObject.trans('Non')));

    updatePhotoCredit();
}

function updatePhotoCredit() {
    var photoCredit = parseInt(jQuery('#mjmt_front_inscription_project_creditPhotos').val());
    var text = photoCredit > 1 ? photoCredit + ' ' + transObject.trans('crédits restants') : photoCredit + ' ' + transObject.trans('crédit restant');

    jQuery('#affichage-credit-restant').html(text);
}

function changeProjectType() {
    var projectType = jQuery('input[name="mjmt_front_inscription_project[projetType]"]:checked').val();
    var selected = false;

    jQuery('#mjmt_frontbundle_projet_inscription_one_page_photo_banque-photo .photo-content').each(function () {
        if (jQuery(this).find('#label-photo-vide').length == 0 && jQuery(this).find('input').attr('data-banque-photo-type-id') != projectType) {
            jQuery(this).hide();
        } else {
            jQuery(this).show();

            if (jQuery(this).find('#label-photo-vide').length == 0 && jQuery('input[name="mjmt_front_inscription_project[banquePhoto][email]"]').val() == '' && !selected) {
                jQuery(this).find('input').prop('checked', true);
                selected = true;
            }
        }
    });

    updateSelectedPhoto();
}

function loadPhoto() {
    jQuery('#mjmt_frontbundle_projet_inscription_one_page_photo_banque-photo label').each(function () {
        if (jQuery(this).find('input').attr('data-photo-web-path')) {
            jQuery(this).append('<img class="image-a-choisir" src="' + jQuery(this).find('input').attr('data-photo-web-path') + '" />');
        }
    });
}

function updateSelectedPhoto() {
    jQuery('#mjmt_frontbundle_projet_inscription_one_page_photo_banque-photo .photo-selected').removeClass('photo-selected');
    jQuery('#mjmt_frontbundle_projet_inscription_one_page_photo_banque-photo .tick-choix-ok').remove();

    var selectedPhoto = jQuery('input[name="mjmt_front_inscription_project[banquePhotos]"]:checked');

    if (selectedPhoto) {
        var selectedPhotoHook = selectedPhoto.closest('div.photo-content');

        if (selectedPhotoHook.find('#label-photo-vide').length == 0) {
            selectedPhotoHook.addClass('photo-selected');
            selectedPhotoHook.find('label').prepend('<img class="tick-choix-ok" src="/front/images/tick-choix.png" />');
            jQuery('input[name="mjmt_front_inscription_project[banquePhoto][email]"]').val('');
            jQuery('input[name="mjmt_front_inscription_project[banquePhoto][nom]"]').val('');
            jQuery('input[name="mjmt_front_inscription_project[banquePhoto][file]"]').val('');

            /*if (jQuery('#mjmt_front_inscription_project_creditPhotos').val() > 0) {
                jQuery('#mjmt_frontbundle_projet_inscription_one_page_photo_upl').slideUp();
            }*/
        }
    }
}
