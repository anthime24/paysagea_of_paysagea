function masquerMenu() {
    jQuery('#entete').css('display', 'none');
}

function afficherMenu() {
    jQuery('#entete').css('display', 'block');
}

function ouvrirPopinAjax(html) {
    jQuery('#popin-finaliser-contenu').html(html);

    toggleFinaliser();
    adaptativeSizeContentFinaliser();
}

function adaptativeSizeContentFinaliser() {
    var heightPopin = jQuery('#popin-finaliser .popin-content').height();
    var heightTitre = jQuery('#popin-finaliser .popin-content-titre').height();
    var newHeight = heightPopin - heightTitre;

    jQuery('#popin-finaliser #popin-finaliser-contenu').css('height', (newHeight - 50) + 'px');
}

function remplacerContenuPopinAjax(html) {
    jQuery('#form-menu').html(html);
    // Positionnnement de départ des PopIn au centre de la page
    centrerDiv('fenetre-menu');
    centrerDiv('fenetre-menu-finaliser');
}

function fermerPopin() {
    jQuery('.popin-menu:not(#popin-menu-video)').fadeOut();
    fermerPopinVideo();
}

function fermerPopinVideo() {
    jQuery('#popin-menu-video').css('display', 'none');
    /*if (/Chrome[\/\s](\d+\.\d+)/.test(navigator.userAgent)){
     jQuery('#popin-menu-video').css('display','none');
     }else{
     jQuery('#popin-menu-video').css('visibility','hidden');
     }*/
}

function ouvrirPopinVideo() {
    if (/Chrome[\/\s](\d+\.\d+)/.test(navigator.userAgent)) {
        jQuery('#popin-menu-video').css('display', 'block');
    } else {
        if (jQuery('#popin-menu-video').css('display') == 'none')
            jQuery('#popin-menu-video').css('display', 'block');
        jQuery('#popin-menu-video').css('visibility', 'visible');
    }
    // Positionnnement de départ des PopIn au centre de la page
    centrerDiv('fenetre-menu-video');
}


function ouvrirPopinQuitter() {
    jQuery('#popin-quitter').fadeIn();
    jQuery('#popin-quitter #quitter-oui').click(function () {
        fermerPopin();
        sauvegarderCreation(jQuery('#menu-sauvegarder-creation a').attr('href'), false, true, false, false, '', '');
    });
    jQuery('#popin-quitter #quitter-non').click(function () {
        fermerPopin();
        location.href = jQuery('#menu-quitter-creation a').attr('href');
    });
    centrerDiv('fenetre-quitter');
}

function replacerPopin() {
    centrerDiv('fenetre-menu');
    centrerDiv('fenetre-menu-finaliser');
    centrerDiv('fenetre-menu-video');
}

function reordonnerCanvas() {
    $('#contenu').find('.entite-accroche').eq(0).before('<div id="tmpReorderEntiteAccroche"></div>');


    var canvasPosition = [];
    jQuery('.entite-accroche').each(function () {
        var id = jQuery(this).attr('id');
        var zIndex = jQuery(this).attr('attr-zindex');

        canvasPosition.push([id, zIndex]);
    });

    //sort in descending order
    canvasPosition.sort(function (a, b) {
        return b[1] - a[1];
    });

    for (var i = 0; i < canvasPosition.length; i++) {
        var currentItem = canvasPosition[i];
        $('#' + currentItem[0]).appendTo('#tmpReorderEntiteAccroche');
    }

    for (var i = 0; i < canvasPosition.length; i++) {
        var currentItemId = canvasPosition[i][0];

        if (i < 1) {
            $('#tmpReorderEntiteAccroche').after($('#' + currentItemId));
        } else {
            var previousItemId = canvasPosition[i - 1][0];
            $('#' + previousItemId).after($('#' + currentItemId));
        }
    }

    $('#tmpReorderEntiteAccroche').remove();
}

function sauvegarderCreation(urlSend, finaliser, quitter, partager, indisponible, reseau, lien) {
    var info = {};
    var i = 0;

    jQuery('.entite-accroche').each(function () {

        var hiddenEntity = false;
        if (!jQuery(this).is(':visible')) {
            hiddenEntity = true;
            jQuery(this).show();
        }

        var img = jQuery(this).find('img').length > 0 ? jQuery(this).find('img') : jQuery(this).find('canvas');
        var x = jQuery(this).position().left;
        var y = jQuery(this).position().top - (img.height() - jQuery(this).height());
        var lasso = 0;

        if (typeof jQuery(this).attr('attr-lasso') != "undefined" && jQuery(this).attr('attr-lasso') == "1") {
            lasso = 1;
        }

        info[i] = {};
        info[i]['div_id'] = jQuery(this).attr('id');
        info[i]['creation_entite_id'] = jQuery(this).attr('attr-creation-entite-id');
        info[i]['entite_id'] = jQuery(this).attr('attr-entite-id');
        info[i]['composition_id'] = jQuery(this).attr('attr-composition-id');
        info[i]['composition_vue_id'] = jQuery(this).attr('attr-composition-vue-id');
        info[i]['lasso'] = lasso;
        info[i]['largeur'] = img.width();
        info[i]['hauteur'] = img.height();
        info[i]['coordonnee_x'] = x;
        info[i]['coordonnee_y'] = y;
        info[i]['symetrie'] = jQuery(this).attr('attr-symetrie');
        info[i]['zindex'] = jQuery(this).attr('attr-zindex');
        info[i]['rotation'] = jQuery(this).attr('style') && jQuery(this).attr('style').indexOf('rotate(') >= 0 ? parseFloat(jQuery(this).attr('style').split('rotate(')[1].split('deg)')[0]) : 0;

        var transformation = '';
        if (jQuery(this).find('div[id^="entite-photo-position"]').attr('style') && jQuery(this).find('div[id^="entite-photo-position"]').attr('style').indexOf('transform') >= 0) {
            var style = jQuery(this).find('div[id^="entite-photo-position"]').attr('style');
            var matches = style != null ? style.match(/transform:([\ \-0-9a-zA-Z\(\)\.]+)/) : null;
            transformation = matches != null && matches.length > 0 ? matches[1] : '';
        }

        info[i]['transformation'] = transformation;
        info[i]['visibilite'] = jQuery(this).attr('attr-visible');
        info[i]['taille_fixe'] = jQuery(this).attr('attr-taille-fixe');
        info[i]['envoyer_image'] = jQuery(this).attr('attr-envoyer-image');

        if (hiddenEntity) {
            jQuery(this).hide();
        }

        if (info[i]['envoyer_image'] == 1) {
            //image base 64
            if (jQuery(this).find('canvas').length > 0) {
                try {
                    info[i]['data_url'] = jQuery(this).find('canvas').get(0).toDataURL();
                } catch (e) {
                    console.debug(e.message);
                }
            } else {
                var canvas = document.createElement("canvas");
                var image = jQuery(this).find('img');
                canvas.width = jQuery(this).attr('attr-resize-width');
                canvas.height = jQuery(this).attr('attr-resize-height');
                canvas.getContext("2d").drawImage(image, 0, 0, jQuery(this).attr('attr-resize-width'), jQuery(this).attr('attr-resize-height'));
                try {
                    info[i]['data_url'] = canvas.toDataURL();
                } catch (e) {

                }
            }
        }
        i++;
    });

    toggleEnregistrement();
    terminerContenu();

    // Correction Htm2Canvas
    window.scrollTo(0, 0);

    reordonnerCanvas();
    // Fin  correction Htm2Canvas

    var html2CanvasScreenshot = jQuery('#html2CanvasScreenshot').val();
    html2CanvasScreenshot = html2CanvasScreenshot.trim();

    if (html2CanvasScreenshot != "" && html2CanvasScreenshot == "true") {
        html2canvas(document.querySelector("#contenu")).then(function (canvas) {
            jQuery.ajax({
                url: urlSend,
                type: 'POST',
                dataType: "json",
                data: {'entites': info, image: canvas.toDataURL()},
                success: function (data) {
                    jQuery.each(data, function (i, item) {
                        jQuery('#' + i).attr('attr-creation-entite-id', item);
                    });
                    jQuery('.entite-accroche').attr('attr-envoyer-image', '0');

                    if (finaliser && lien) {
                        var url = lien;

                        jQuery.ajax({
                            url: url,
                            success: function (html) {
                                toggleEnregistrement();
                                ouvrirPopinAjax(html);
                            },
                            error: function (data) {
                                toggleEnregistrement();
                            }
                        });

                        return false;
                    } else if (quitter) {
                        toggleEnregistrement();
                        location.href = jQuery('#entete-icones-quitter').attr('href');
                    } else if (partager) {
                        toggleEnregistrement();
                    } else if (indisponible) {
                        location.href = jQuery('#url-reprendre-offre').val();
                    } else {
                        toggleEnregistrement();
                    }
                },
                error: function (data) {
                    toggleEnregistrement();
                }
            });
        });
    } else {
        jQuery.ajax({
            url: urlSend,
            type: 'POST',
            dataType: "json",
            data: {'entites': info, screenshotHtmlContent: "true"},
            success: function (data) {
                jQuery.each(data, function (i, item) {
                    jQuery('#' + i).attr('attr-creation-entite-id', item);
                });
                jQuery('.entite-accroche').attr('attr-envoyer-image', '0');

                if (finaliser && lien) {
                    var url = lien;

                    jQuery.ajax({
                        url: url,
                        success: function (html) {
                            toggleEnregistrement();
                            ouvrirPopinAjax(html);
                        },
                        error: function (data) {
                            toggleEnregistrement();
                        }
                    });

                    return false;
                } else if (quitter) {
                    toggleEnregistrement();
                    location.href = jQuery('#entete-icones-quitter').attr('href');
                } else if (partager) {
                    toggleEnregistrement();
                } else if (indisponible) {
                    location.href = jQuery('#url-reprendre-offre').val();
                } else {
                    toggleEnregistrement();
                }
            },
            error: function (data) {
                toggleEnregistrement();
            }
        });
    }
}

function refreshAuthenticatedUser() {
    var url = $('#entete .connectedUserInfo').attr('data-url');

    $.ajax({
        type: 'GET',
        url: url,
        dataType: "json",
        success: function (data) {
            $('#entete .connectedUserInfo').html(data['prenom'] + ' ' + data['nom']);
        }
    });
}

function afficherErreur(erreurMsg, erreurMsgTitre, callback) {

    if (typeof (erreurMsgTitre) == "undefined" || erreurMsgTitre == null) {
        erreurMsgTitre = "Erreur";
    }

    var hasCallback = true;
    if (typeof (callback) == "undefined" || callback == null) {
        hasCallback = false;
    }

    if (hasCallback == true) {
        jQuery('#popin-error').find('.croix-ferme').hide();
        jQuery('#popin-error').find('.confirm-button').show();

        jQuery(document).off('click', '#popin-error-button-ok');
        jQuery(document).on('click', '#popin-error-button-ok', function (e) {
            e.preventDefault();
            e.stopPropagation();

            callback("continue");
        });

        jQuery(document).off('click', '#popin-error-button-ko');
        jQuery(document).on('click', '#popin-error-button-ko', function (e) {
            e.preventDefault();
            e.stopPropagation();

            callback("abort");
        });
    } else {
        jQuery('#popin-error').find('.croix-ferme').show();
        jQuery('#popin-error').find('.confirm-button').hide();
    }

    jQuery('#popin-error').find('.msg-titre').html(erreurMsgTitre);
    jQuery('#popin-error').find('.msg').html(erreurMsg);
    jQuery('#popin-error').fadeIn("slow");

    initialiserFenetreSelection(jQuery('#popin-error').find('.popin-content-contenu'));
}

jQuery(document).ready(function () {

    jQuery('#popin-error .croix-ferme').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        jQuery('#popin-error').fadeOut("slow");
        return false;
    });

    // Pour les tablettes on initialise en plus le click
    jQuery('#entete ul li').click(function () {
        if (jQuery(this).find('ul').length > 0 && !jQuery(this).hasClass('current')) {
            jQuery('#entete ul li').removeClass('current');
            jQuery(this).addClass('current');
        } else if (!jQuery(this).hasClass('current')) {
            jQuery('#entete ul li').removeClass('current');
        } else {
            jQuery(this).removeClass('current');
        }
    });

    // Clic sur un bouton du Menu
    jQuery('body').on('click', '#entete > ul > li > ul > li.fenetre-form a', function () {
        replierToutElementFixed();
        var url = $(this).attr('href');

        jQuery.ajax({
            url: url
        }).done(function (html) {
            ouvrirPopinAjax(html);
        });

        return false;
    });

    // Affichage de la vidéo
    jQuery('body').on('click', '#menu-video', function () {
        replierToutElementFixed();
        ouvrirPopinVideo();
        if (jQuery(this).hasClass('pause')) {
            callPlayer(jQuery('.mjmt-app-aide-video.current').find('iframe').attr('id'), function () {
                callPlayer(jQuery('.mjmt-app-aide-video.current').find('iframe').attr('id'), "playVideo");
            });
        }
    });

    // Sauvegarder la création
    jQuery('body').on('click', '#menu-bloc-actions-enregistrer a', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var lien = jQuery(this).attr('attr-href');

        menuItemClickedEvent(e).then(function (shouldContinue) {
            if (shouldContinue == true) {
                sauvegarderCreation(lien, false, false, false, false, '', '');
            }
        });

        return false;
    });

    jQuery('body').on('click', '#entete-icones-facebook, #entete-icones-pinterest', function () {
        sauvegarderCreation(jQuery('#menu-bloc-actions-enregistrer a').attr('attr-href'), false, false, true, false, jQuery(this).attr('href'), '');

        return true;
    });

    // Finaliser la création
    jQuery('body').on('click', '#menu-bloc-finalise a, #menu-bloc-actions-retour a', function (e) {
        var lien = jQuery(this).attr('href');

        menuItemClickedEvent(e).then(function (shouldContinue) {
            if (shouldContinue == true) {
                sauvegarderCreation(jQuery('#menu-bloc-actions-enregistrer a').attr('attr-href'), true, false, false, false, '', lien);
            }
        })

        return false;
    });

    jQuery('map[name="progression-map"]').find('area').on('click', function (e) {
        var step = $(this).attr('data-step');

        if (step == '3') {
            var lien = $(this).attr('href');
            sauvegarderCreation(jQuery('#menu-bloc-actions-enregistrer a').attr('attr-href'), true, false, false, false, '', lien);
        }

        e.preventDefault();
        e.stopPropagation();
        return false;
    });

    // Quitter l'application
    jQuery('body').on('click', '#entete-icones-quitter', function () {
        if (jQuery('#acces-complet-client').val() != 1) {
            toggleEntiteErreurMaximum();
            jQuery('#selection-entite-erreur-maximum #confirmation-non').click(function () {
                toggleEntiteErreurMaximum();
            });
        } else {
            toggleQuitter();

            jQuery('#popin-quitter #popin-quitter-ko').click(function () {
                toggleQuitter();
                jQuery(this).unbind("click");
                jQuery('#popin-quitter #popin-quitter-ok').unbind("click");
                location.href = jQuery('#entete-icones-quitter').attr('href');
            });

            jQuery('#popin-quitter #popin-quitter-ok').click(function () {
                toggleQuitter();
                jQuery(this).unbind("click");
                jQuery('#popin-quitter #popin-quitter-ko').unbind("click");
                sauvegarderCreation(jQuery('#menu-bloc-actions-enregistrer a').attr('attr-href'), false, true, false, false, '', '');
            });
        }

        return false;
    });

    // Affichage des points de repères
    jQuery('body').on('click', '#menu-repere', function (e) {
        e.preventDefault();
        e.stopPropagation();

        menuItemClickedEvent(e).then(function (shouldContinue) {
            if (shouldContinue == true) {
                initialiserProportion(false);
            }
        });

    });

    jQuery('body').on('click', '#popin-menu-video #diminuer', function () {
        jQuery('#menu-video').addClass('pause');
        callPlayer(jQuery('.mjmt-app-aide-video.current').find('iframe').attr('id'), function () {
            callPlayer(jQuery('.mjmt-app-aide-video.current').find('iframe').attr('id'), "stopVideo");
        });
        fermerPopinVideo();
    });

    jQuery('body').on('click', '#croix-fermer', function () {
        jQuery('#menu-video').removeClass('pause');

        var video = jQuery('.mjmt-app-aide-video.current').find('iframe').attr("src");
        jQuery('.mjmt-app-aide-video.current').find('iframe').attr("src", "");
        jQuery('.mjmt-app-aide-video.current').find('iframe').attr("src", video);

        fermerPopinVideo();
        fermerPopin();
    });

    jQuery('body').on('click', 'p.mjmt-app-aide-titre-video', function () {
        if (!jQuery('div#mjmt-app-aide-video-' + jQuery(this).attr('attr-video-id')).hasClass('current')) {
            jQuery('div.mjmt-app-aide-video.current').slideUp().removeClass('current');
            jQuery('div#mjmt-app-aide-video-' + jQuery(this).attr('attr-video-id')).slideDown().addClass('current');
        }

    });

    // Validation des formulaires de paramétrage et de rendu de la création
    jQuery('body').on('click', '.form-menu input[type="submit"]', function () {
        var formulaire = jQuery(this).closest('form').serialize();
        var url = jQuery(this).closest('form').attr('action');
        var form = jQuery(this).closest('form');

        if (jQuery(form).hasClass('form-menu-finaliser-connexion-client')) {
            var redirectToOfferSelection = false;
            if (jQuery(form).parent().find('input[name="redirectToOfferSelection"]').length > 0 && jQuery(form).parent().find('input[name="redirectToOfferSelection"]').val() == 1) {
                redirectToOfferSelection = true;
            }

            jQuery.ajax({
                url: url + '?ajaxResponse=true',
                type: 'POST',
                data: formulaire
            }).done(function (data) {
                if (data == 'success') {
                    jQuery.ajax({
                        url: jQuery(form).find('input[name=url_lier_creation_client]').val()
                    }).done(function (data) {
                        jQuery('#popin-finaliser .popin-content').css('width', '50%');
                        jQuery('#popin-finaliser-contenu').html(data);

                        if (redirectToOfferSelection === true) {
                            location.href = jQuery('#url-redirection-page-offre').val();
                            return;
                        }
                    });


                    refreshAuthenticatedUser();
                } else {
                    alert('Email et/ou mot de passe incorrect.');
                }
            });
        } else if (jQuery(form).hasClass('form-menu-parametrage')) {
            jQuery.ajax({
                url: url,
                type: 'POST',
                data: formulaire
            }).done(function (data) {
                if (data === 'ok') {
                    toggleParametrage();

                    var soleil = jQuery('#marque_blanche_appbundle_creation_ensoleillement').val();
                    var typeCreation = jQuery('#marque_blanche_appbundle_creation_creation_type').val();
                    var style = jQuery('#marque_blanche_appbundle_creation_style').val();
                    var arrosageDeuxFoisSemaine = jQuery('#marque_blanche_appbundle_creation_arrosageDeuxFoisSemaine').is(':checked');

                    updateDefaultSetting(soleil, typeCreation, style, arrosageDeuxFoisSemaine);

                }
            });
        } else if (jQuery(form).hasClass('form-menu-finaliser-nouveau-client')) {
            jQuery.ajax({
                url: url,
                type: 'POST',
                data: formulaire
            }).done(function (data) {
                jQuery('#popin-finaliser .popin-content').css('width', '50%');
                jQuery('#popin-finaliser-contenu').html(data);

                refreshAuthenticatedUser();
            });
        } else if (jQuery(form).hasClass('form-menu-option-rendu')) {
            jQuery.ajax({
                url: url,
                type: 'POST',
                data: formulaire
            }).done(function (data) {
                if (data === 'ok') {
                    toggleRendu();
                }
            });
        } else {
            jQuery.ajax({
                url: url,
                type: 'POST',
                data: formulaire
            }).done(function (data) {
                toggleFinaliser();
            });
        }

        return false;
    });

    jQuery(window).resize(function () {
        replacerPopin();
    });

    jQuery('body').on('click', '#menu-bloc-actions-outils > a', function () {
        if (jQuery('#menu-bloc-actions-outils').hasClass('open')) {
            jQuery('#menu-bloc-actions-outils').removeClass('open');
            jQuery('#menu-bloc-actions-outils').animate({
                'height': '56px'
            });
        } else {
            jQuery('#menu-bloc-actions-outils').addClass('open');
            jQuery('#menu-bloc-actions-outils').animate({
                'height': '235px'
            });
        }

        return false;
    });

    jQuery('body').on('click', '#menu-parametrage-creation', function (e) {

        var url = jQuery(this).attr('href');

        menuItemClickedEvent(e).then(function (shouldContinue) {
            if (shouldContinue == true) {
                jQuery.ajax({
                    url: url
                }).done(function (html) {
                    jQuery('#popin-parametrage').html(html);
                    toggleParametrage();
                });
            }
        });

        return false;
    });

    jQuery('body').on('click', '#menu-rendu-creation', function () {
        var url = jQuery(this).attr('href');

        jQuery.ajax({
            url: url
        }).done(function (html) {
            jQuery('#popin-rendu').html(html);
            toggleRendu();
        });

        return false;
    });

    jQuery('body').on('click', '.croix-ferme', function () {
        if (jQuery(this).closest('.popin').attr('id') == 'popin-finaliser') {
            var url = $('#application_refresh_url');
            window.location.attr.href = url;
        }
    });

    jQuery('body').on('click', '#menu-bloc-actions-lasso-disabled', function (e) {
        e.preventDefault();

        jQuery('#fonction-non-disponible-offre-decouverte').fadeIn("slow");
        initialiserFenetreSelection(jQuery('#fonction-non-disponible-offre-decouverte').find('.popin-content-contenu'));

        jQuery('#fonction-non-disponible-offre-decouverte-button-ko').off('click');
        jQuery('#fonction-non-disponible-offre-decouverte-button-ko').on('click', function (e) {
            e.preventDefault();
            jQuery('#fonction-non-disponible-offre-decouverte').fadeOut("slow");
            return false;
        });

        jQuery('#fonction-non-disponible-offre-decouverte-button-ok').off('click');
        jQuery('#fonction-non-disponible-offre-decouverte-button-ok').on('click', function (e) {
            e.preventDefault();
            var lienFinaliser = jQuery('#url-finalisation-creation-compte').val() + '?redirectToOfferSelection=true';

            jQuery('#fonction-non-disponible-offre-decouverte').fadeOut("slow");
            sauvegarderCreation(jQuery('#menu-bloc-actions-enregistrer a').attr('attr-href'), true, false, false, true, '', lienFinaliser);
            return false;
        });

        return false;
    });
});
