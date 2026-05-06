function initialiserProportion(firstTime) {
    var width = 0;
    var height = 0;
    var top = 0;
    var left = 0;
    var widthContenu = jQuery('#contenu').width();
    var heightContenu = jQuery('#contenu').height();
    var widthPointProportion = 200;
    var heightPointProportion = 600;

    if (!debloquerAction())
        return false;

    terminerAction();
    masquerEntites();
    terminerContenu();
    replierInterface();


    // Empêche l'apparition de deux instances des repères
    if (jQuery('#accroche-point-proportion-taille').is(':visible') && jQuery('#accroche-point-proportion-validation').is(':visible')) {
        reinitialiserProportion();
        return;
    }


    if (firstTime) {
        height = heightContenu / 4;
        width = (widthPointProportion * (height * 100 / heightPointProportion)) / 100;
        left = (widthContenu / 4) - (width / 2);
        jQuery('#contenu').append('<div id="repere1" class="accroche-point-proportion" style="top:' + top + 'px;left:' + left + 'px;"><img class="point-proportion" src="/application/images/homme.png" style="width:' + width + 'px;height:' + height + 'px"  /></div>');

        left = widthContenu - left - width;
        jQuery('#contenu').append('<div id="repere2" class="accroche-point-proportion" style="top:' + top + 'px;left:' + left + 'px;"><img class="point-proportion" src="/application/images/homme.png" style="width:' + width + 'px;height:' + height + 'px" /></div>');

        top = heightContenu / 2;
        left = 0;
        height = top;
        width = (widthPointProportion * (height * 100 / heightPointProportion)) / 100;
        jQuery('#contenu').append('<div id="repere3" class="accroche-point-proportion" style="top:' + top + 'px;left:' + left + 'px;"><img class="point-proportion" src="/application/images/homme.png" style="width:' + width + 'px;height:' + height + 'px" /></div>');

        left = widthContenu - width;
        jQuery('#contenu').append('<div id="repere4" class="accroche-point-proportion" style="top:' + top + 'px;left:' + left + 'px;"><img class="point-proportion" src="/application/images/homme.png" style="width:' + width + 'px;height:' + height + 'px" /></div>');

    } else {
        for (var i = 1; i <= 4; i++) {
            left = jQuery('input[name="repere' + i + '_x"]').val();
            top = jQuery('input[name="repere' + i + '_y"]').val();
            width = jQuery('input[name="repere' + i + '_largeur"]').val() - 6; //-6px correspondant à la bordure 
            height = jQuery('input[name="repere' + i + '_hauteur"]').val() - 6; //-6px correspondant à la bordure 
            jQuery('#contenu').append('<div id="repere' + i + '" class="accroche-point-proportion" style="top:' + top + 'px;left:' + left + 'px;"><img class="point-proportion" src="/application/images/homme.png" style="width:' + width + 'px;height:' + height + 'px" /></div>');
        }
    }

    if (firstTime && premierDemarrage()) {
        if(jQuery('html').attr('lang') == 'fr') {
            lancerVideoAide('proportion', 'open');
        }
    }

    initialiserProportionDraggable();
    initialiserProportionResizeable();

    jQuery('body').append('<div id="accroche-point-proportion"></div>');
//    centrerDivHorizontal('accroche-point-proportion');

    var contenuTexte = translateJs('repere.header');

    if(premierDemarrageSansProportion()) {
        contenuTexte = contenuTexte + '<br /><span class="step">' + translateJs('repere.header2') + '</span>';
    }

    jQuery('#entete-outil').html('');
    jQuery('#entete-outil').append('<div id="accroche-point-proportion-message">' +
        '<div class="menu-bloc-logo"><img src="/application/images/logo.png"></div>' +
        '<div class="menu-bloc-content">' +
        '<div class="container">' +
        '<p>' +  contenuTexte + '</p>' +
        '</p>' +
        '<div>' +
        '</div>' +
        '</div>');
    jQuery('#entete-outil').show();
    //centrerDivHorizontal('accroche-point-proportion-message');

    jQuery('#accroche-point-proportion').append('<div id="accroche-point-proportion-taille"> ' +
        '<div class="tool-button"><div id="acp-agrandir"></div><label>' + translateJs('menuoutil.agrandir') + '</label></div>' +
        '<div class="tool-button"><div id="acp-diminuer"></div><label>' + translateJs('menuoutil.retrecir') + '</label></div>' +
        '</div>');

    jQuery('#accroche-point-proportion').append('<div id="accroche-point-proportion-validation"> ' +
        '<div class="tool-button"><div id="acp-reset"></div><label>' + translateJs('menuoutil.reset') + '</label></div>' +
        '<div class="tool-button"><div id="acp-annulation"></div><label>' + translateJs('menuoutil.annuler') + '</label></div>' +
        '<div class="tool-button"><div id="acp-validation"></div><label>' + translateJs('menuoutil.confirmer') + '</label></div>' +
        '</div>');

//    masquerMenu();
//    masquerMaSelection();

    jQuery('#acp-validation').click(function () {
        sauvegarderProportion();
    });

    jQuery('#acp-reset').click(function () {
        reinitialiserProportion();
    });

    jQuery('#acp-annulation').click(function () {
        terminerProportion();
        finirPremierDemarrageSansProportion();
    });

    jQuery('body').on('mouseup', '#accroche-point-proportion-taille div', function () {
        //debloquerAction();

        var idAction = jQuery(this).attr('id');

        switch (idAction) {
            case 'acp-agrandir':
                clearInterval(interval);
                break;

            case 'acp-diminuer':
                clearInterval(interval);
                break;
        }
    });

    // Listener pour arréter l'agrandissement ou diminution automatique
    jQuery('body').on('mouseleave', '#accroche-point-proportion-taille div', function () {
        clearInterval(interval);
    }).mouseup(function () {
        clearInterval(interval);
    });

    jQuery('body').on('mousedown', '#accroche-point-proportion-taille div', function () {

        var idRepere = jQuery('#contenu .actif').attr('id');
        var idAction = jQuery(this).attr('id');

        switch (idAction) {
            case 'acp-agrandir':
                clearInterval(interval);
                agrandirRepere(idRepere);
                interval = setInterval("agrandirRepere('" + idRepere + "')", 50);
                break;

            case 'acp-diminuer':
                clearInterval(interval);
                diminuerRepere(idRepere);
                interval = setInterval("diminuerRepere('" + idRepere + "')", 50);
                break;
        }
    });


    // Initialisation des tipsy
    if (!isTouchDevice()) {
        jQuery('.acp-tipsy').tipsy({
            gravity: 's',
            opacity: 1
        });
    }
}

function agrandirRepere(idRepere) {
    resizeRepere(idRepere, 2);
}

function diminuerRepere(idRepere) {
    resizeRepere(idRepere, -2);
}

function resizeRepere(idRepere, valeur) {
    var hauteurImageFond = jQuery('#contenu-fond-image').height();
    var largeurImageFond = jQuery('#contenu-fond-image').width();
    var ancienneHauteur = parseInt(jQuery('#' + idRepere + ' img').css('height'));
    var nouvelleHauteur = ancienneHauteur + valeur;
    var bordure = 6;

    if (nouvelleHauteur < 50) {
        nouvelleHauteur = 50;
    } else if (nouvelleHauteur > (hauteurImageFond - bordure)) {
        nouvelleHauteur = (hauteurImageFond - bordure);
    }

    jQuery('#' + idRepere + ' img').css('width', 'auto');
    jQuery('#' + idRepere + ' img').css('height', nouvelleHauteur);
    jQuery('#' + idRepere + ' .ui-wrapper').css('width', 'auto');
    jQuery('#' + idRepere + ' .ui-wrapper').css('height', nouvelleHauteur + bordure);

    if (ancienneHauteur != nouvelleHauteur) {
        var ancienTop = parseInt(jQuery('#' + idRepere).position().top);
        var ancienLeft = parseInt(jQuery('#' + idRepere).position().left);
        var nouveauTop = ancienTop - valeur;

        if (nouveauTop < 0) {
            nouveauTop = 0;
        } else if (nouveauTop + (nouvelleHauteur + bordure) > hauteurImageFond) {
            nouveauTop = hauteurImageFond - (nouvelleHauteur + bordure);
        }

        jQuery('#' + idRepere).css('top', nouveauTop);

        var nouvelleLargeur = parseInt(jQuery('#' + idRepere + ' img').width());

        if (ancienLeft + (nouvelleLargeur + bordure) > largeurImageFond) {
            var nouveauLeft = largeurImageFond - (nouvelleLargeur + bordure);
            jQuery('#' + idRepere).css('left', nouveauLeft);
        }
    }
}

function initialiserProportionDraggable() {
    jQuery('.accroche-point-proportion').draggable({
        appendTo: 'body',
        containment: "#contenu",
        start: function (event, ui) {
            isDraggingMedia = true;
        },
        stop: function (event, ui) {
            isDraggingMedia = false;
            // blah
        }
    });
}

function initialiserProportionResizeable() {
    jQuery('.point-proportion').resizable({
        aspectRatio: true,
        handles: "ne,n",
        containment: "#contenu",
        minWidth: 50
    });
}

function reinitialiserProportion() {
    terminerProportion();
    initialiserProportion(true);
}

function terminerProportion() {
    jQuery('.accroche-point-proportion').remove();
    jQuery('#accroche-point-proportion').remove();
    jQuery('#accroche-point-proportion-validation').remove();
    jQuery('#accroche-point-proportion-taille').remove();
    jQuery('#accroche-point-proportion-message').remove();
    jQuery('#entete-outil').html('');
    jQuery('#entete-outil').hide();

    if (!isTouchDevice())
        jQuery('.tipsy').remove();

    deplierInterface();
    afficherEntites();
}

function sauvegarderProportion() {
    // 1 - haut gauche
    // 2 - haut droit
    // 3 - bas gauche
    // 4 - bac droit
    var coordonnees = '';
    var i = 1;
    jQuery('.accroche-point-proportion').each(function () {
        coordonnees += (coordonnees != '' ? '&' : '') + 'coordonnees[' + i + '][top]=' + jQuery(this).css('top').replace('px', '');
        coordonnees += '&coordonnees[' + i + '][left]=' + jQuery(this).css('left').replace('px', '');
        coordonnees += '&coordonnees[' + i + '][width]=' + jQuery(this).width();
        coordonnees += '&coordonnees[' + i + '][height]=' + jQuery(this).height();
        i++;
    });

    if (jQuery('input[name="initialisation_proportions_admin"]').length == 1)
        coordonnees += '&initialisation=proportions';

    jQuery.ajax({
        url: jQuery('#url-replacer-repere').val(),
        type: 'GET',
        data: coordonnees,
        dataType: 'html'
    }).done(function (html) {
        if (html == '1') {
            var i = 1;

            jQuery('.accroche-point-proportion').each(function () {
                jQuery('input[name="repere' + i + '_x"]').val(jQuery(this).css('left').replace('px', ''));
                jQuery('input[name="repere' + i + '_y"]').val(jQuery(this).css('top').replace('px', ''));
                jQuery('input[name="repere' + i + '_largeur"]').val(jQuery(this).width());
                jQuery('input[name="repere' + i + '_hauteur"]').val(jQuery(this).height());
                i++;
            });
            terminerProportion();

            if (premierDemarrageSansProportion()) {
                initialiserTampon(true);
            } else {
                redimensionnerEntiteApresRepere();
            }

        }
    }).fail(function (html) {
        alert('Une erreure est survenue pendant la sauvegarde des repères');
    });
}

function recupererPlusGrandRepere() {
    var point = '';
    var plusGrandeValeur = 0;

    jQuery('#informations-proportion input[name$="_hauteur"]').each(function () {
        if (parseFloat(jQuery(this).val()) > plusGrandeValeur) {
            plusGrandeValeur = parseFloat(jQuery(this).val());
            point = jQuery(this).attr('name').replace("repere", "").replace("_hauteur", "");
        }
    });

    var x = parseFloat(jQuery('#informations-proportion input[name="repere' + point + '_x"]').val()) + (parseFloat(jQuery('#informations-proportion input[name="repere' + point + '_largeur"]').val()) / 2);
    var y = parseFloat(jQuery('#informations-proportion input[name="repere' + point + '_y"]').val()) + parseFloat(jQuery('#informations-proportion input[name="repere' + point + '_hauteur"]').val());

    return {'x': x, 'y': y};
}

function recupererPlusPetitRepere() {
    var point = '';
    var plusPetitValeur = 0;

    jQuery('#informations-proportion input[name$="_hauteur"]').each(function () {
        if (parseFloat(jQuery(this).val()) < plusPetitValeur || plusPetitValeur == 0) {
            plusPetitValeur = parseFloat(jQuery(this).val());
            point = jQuery(this).attr('name').replace("repere", "").replace("_hauteur", "");
        }
    });

    var x = parseFloat(jQuery('#informations-proportion input[name="repere' + point + '_x"]').val()) + (parseFloat(jQuery('#informations-proportion input[name="repere' + point + '_largeur"]').val()) / 2);
    var y = parseFloat(jQuery('#informations-proportion input[name="repere' + point + '_y"]').val()) + parseFloat(jQuery('#informations-proportion input[name="repere' + point + '_hauteur"]').val());

    return {'x': x, 'y': y};
}

function redimensionnerEntiteApresRepere() {
    var idEntite;
    jQuery('div#contenu div.entite-accroche').each(function () {
        idEntite = $(this).attr('id').split("entite-photo-");
        redimensionnerAutoEntite(idEntite[1]);
    });
}


jQuery(document).ready(function () {
    jQuery('#contenu-fond-image img').on('load', function () {
        if (premierDemarrageSansProportion()) {
            var first = jQuery('input[name="repere1_x"]').val() == '' || jQuery('input[name="repere1_x"]').val() == undefined;
            initialiserProportion(first);
        } else if (premierDemarrage()) {
            ouvrirSelection();
        }
    }).each(function () {
        if (this.complete) $(this).load();
    });

    jQuery('#contenu').on('mousedown', '.accroche-point-proportion', function () {
        jQuery('#accroche-point-proportion-taille').css('display', 'block');
        jQuery('.accroche-point-proportion').removeClass('actif');
        jQuery(this).addClass('actif');
    });
});

$(window).resize(function () {
//    centrerDivHorizontal('accroche-point-proportion');
    //centrerDivHorizontal('accroche-point-proportion-message');
});
