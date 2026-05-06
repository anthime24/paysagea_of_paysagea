// Fonction permettant de replier tous éléments en position fixed
function replierToutElementFixed() {
    fermerMaSelection();
    fermerSelection();
    fermerPopin();
    fermerPopinDescription();
}

// Centre automatiquement l'élément avec l'id passé en paramètre horizontalement
function centrerDivHorizontal(element) {
    var menuWidth = jQuery('#menu').width();

    var largeurFenetre = jQuery(window).width() > jQuery('body').width() ? jQuery('body').css('minHeight') : jQuery('body').width();
    var positionGauche = parseFloat((largeurFenetre - jQuery('#' + element).width()) / 2);
    positionGauche = positionGauche + parseFloat(menuWidth / 2);
    jQuery('#' + element).css('left', positionGauche + 'px');
}

// Centre automatiquement l'élément avec l'id passé en paramètre vertical
function centrerDivVerticalement(element) {
    var hauteurFenetre = 0;
    if (jQuery('#' + element).css('position') == 'fixed')
        hauteurFenetre = jQuery(window).height();
    else
        hauteurFenetre = jQuery(window).height() > jQuery('body').height() ? jQuery(window).height() : jQuery('body').height();

    var positionHaut = (hauteurFenetre / 2) - (jQuery('#' + element).height() / 2);
    if (jQuery('#entete').height() > positionHaut)
        jQuery('#' + element).css('top', jQuery('#entete').height() + 'px');
    else
        jQuery('#' + element).css('top', positionHaut + 'px');


}

// Récupère le top de l'élément avec l'id passé en paramètre pour le contrer verticalement
function recupereTopCentrer(element) {
    var hauteurFenetre = jQuery(window).height();
    var positionHaut = (hauteurFenetre / 2) - (jQuery('#' + element).height() / 2);

    return positionHaut;
}

// Centre automatiquement l'élément avec l'id passé en paramètre
function centrerDiv(element) {
    var hauteurFenetre = jQuery(window).height() > jQuery('body').height() ? jQuery(window).height() : jQuery('body').height();
    var largeurFenetre = jQuery(window).width() > jQuery('body').width() ? jQuery('body').css('minHeight') : jQuery('body').width();
    var positionHaut = (hauteurFenetre / 2) - (jQuery('#' + element).height() / 2);
    var positionGauche = (largeurFenetre / 2) - (jQuery('#' + element).width() / 2);
    jQuery('#' + element).css('top', positionHaut + 'px');
    jQuery('#' + element).css('left', positionGauche + 'px');
}

// Centre automatiquement l'élément avec l'id passé en paramètre
function centrerDivAvecParent(element, elementParent) {
    var hauteurFenetre = jQuery('#' + elementParent).height();
    var largeurFenetre = jQuery('#' + elementParent).width();
    var positionHaut = (hauteurFenetre / 2) - (jQuery('#' + element).height() / 2);
    var positionGauche = (largeurFenetre / 2) - (jQuery('#' + element).width() / 2);
    jQuery('#' + element).css('top', positionHaut + 'px');
    jQuery('#' + element).css('left', positionGauche + 'px');
}

// Premier démarrage sans proportion
function premierDemarrageSansProportion() {
    return jQuery('input[name="initialisation_application"]').val() == '1' ? true : false;
}

// Premier démarrage de l'appli sans enregistrement préalable
function premierDemarrage() {
    if (jQuery('#premier-demarrage').val() == '1') {
        return false;
    } else {
        return true;
    }
}

// Finir premier démarrage
function finirPremierDemarrage() {
    //TODO à completer
}

// Finir premier démarrage sans proportion
function finirPremierDemarrageSansProportion() {
    jQuery('input[name="initialisation_application"]').val('0');
}

// Détection des devices en mode touch event
function isTouchDevice() {
    return 'ontouchstart' in window || ('onmsgesturechange' in window && window.navigator.msMaxTouchPoints > 0);
}

//Detetecte si le canvas est supporté par le navigateurs
function isCanvasSupported() {
    var elem = document.createElement('canvas');
    return !!(elem.getContext && elem.getContext('2d'));
}

function callPlayer(frame_id, func, args) {
    if (window.jQuery && frame_id instanceof jQuery)
        frame_id = frame_id.get(0).id;
    var iframe = document.getElementById(frame_id);
    if (iframe && iframe.tagName.toUpperCase() != 'IFRAME') {
        iframe = iframe.getElementsByTagName('iframe')[0];
    }

    // When the player is not ready yet, add the event to a queue
    // Each frame_id is associated with an own queue.
    // Each queue has three possible states:
    //  undefined = uninitialised / array = queue / 0 = ready
    if (!callPlayer.queue)
        callPlayer.queue = {};
    var queue = callPlayer.queue[frame_id],
        domReady = document.readyState == 'complete';

    if (domReady && !iframe) {
        // DOM is ready and iframe does not exist. Log a message
        window.console && console.log('callPlayer: Frame not found; id=' + frame_id);
        if (queue)
            clearInterval(queue.poller);
    } else if (func === 'listening') {
        // Sending the "listener" message to the frame, to request status updates
        if (iframe && iframe.contentWindow) {
            func = '{"event":"listening","id":' + JSON.stringify('' + frame_id) + '}';
            iframe.contentWindow.postMessage(func, '*');
        }
    } else if (!domReady ||
        iframe && (!iframe.contentWindow || queue && !queue.ready) ||
        (!queue || !queue.ready) && typeof func === 'function') {
        if (!queue)
            queue = callPlayer.queue[frame_id] = [];
        queue.push([func, args]);
        if (!('poller' in queue)) {
            // keep polling until the document and frame is ready
            queue.poller = setInterval(function () {
                callPlayer(frame_id, 'listening');
            }, 250);
            // Add a global "message" event listener, to catch status updates:
            messageEvent(1, function runOnceReady(e) {
                if (!iframe) {
                    iframe = document.getElementById(frame_id);
                    if (!iframe)
                        return;
                    if (iframe.tagName.toUpperCase() != 'IFRAME') {
                        iframe = iframe.getElementsByTagName('iframe')[0];
                        if (!iframe)
                            return;
                    }
                }
                if (e.source === iframe.contentWindow) {
                    // Assume that the player is ready if we receive a
                    // message from the iframe
                    clearInterval(queue.poller);
                    queue.ready = true;
                    messageEvent(0, runOnceReady);
                    // .. and release the queue:
                    while (tmp = queue.shift()) {
                        callPlayer(frame_id, tmp[0], tmp[1]);
                    }
                }
            }, false);
        }
    } else if (iframe && iframe.contentWindow) {
        // When a function is supplied, just call it (like "onYouTubePlayerReady")
        if (func.call)
            return func();
        // Frame exists, send message
        iframe.contentWindow.postMessage(JSON.stringify({
            "event": "command",
            "func": func,
            "args": args || [],
            "id": frame_id
        }), "*");
    }

    /* IE8 does not support addEventListener... */
    function messageEvent(add, listener) {
        var w3 = add ? window.addEventListener : window.removeEventListener;
        w3 ?
            w3('message', listener, !1)
            :
            (add ? window.attachEvent : window.detachEvent)('onmessage', listener);
    }
}

function toggleMaSelection() {
    if (jQuery('#bloc-ma-selection').width() <= 0) {
        jQuery('#menu-lien-liste-selection').css('background-image', 'url(/application/images/icone/fleche-fermeture.png)');
        jQuery('#bloc-ma-selection').animate({
            width: '480px'
        });
    } else {
        jQuery('#menu-lien-liste-selection').css('background-image', 'url(/application/images/icone/fleche-acces.png)');
        jQuery('#bloc-ma-selection').animate({
            width: '0px'
        });
    }
}

function getArrayDifference(a1, a2) {

    var a = [], diff = [];

    for (var i = 0; i < a1.length; i++) {
        a[a1[i]] = true;
    }

    for (var i = 0; i < a2.length; i++) {
        if (a[a2[i]]) {
            delete a[a2[i]];
        } else {
            a[a2[i]] = true;
        }
    }

    for (var k in a) {
        diff.push(k);
    }

    return diff;
}

function getSelectArrayValues(value) {
    var values = [];
    if (value.indexOf(',') != -1) {
        values = value.split(',');
    } else {
        values.push(value);
    }

    return values;
}

function compareSelectValues(value1, value2) {
    var different = false;
    var values1 = getSelectArrayValues(value1);
    var values2 = getSelectArrayValues(value2);

    if (values1.length != values2.length) {
        different = true;
    } else {
        var diff = getArrayDifference(values1, values2);
        if (diff.length > 0) {
            different = true;
        }
    }

    return different;
}

function displayPopinAideGlobal() {
    if (jQuery('html').attr('lang') == 'fr') {
        if (!jQuery('#popin-aide').is(':visible') && jQuery('#popin-aide-contenu').find('.mjmt-app-aide-video').length > 0) {
            if (jQuery('#popin-aide-contenu').find('.mjmt-app-aide-video[attr-video-outils="application"]').length > 0) {
                var outil = "application";
            } else {
                var outil = jQuery('#popin-aide-contenu').find('.mjmt-app-aide-video').eq(0).attr('attr-video-outils');
            }

            lancerVideoAide(outil, 'open');
        }
    }
}

jQuery(document).ready(function () {
    //affiche la popin d'aide sur la rubrique général si aucun autre script ne l'a affiché
    if (premierDemarrage()) {
        var popinAideGlobalTimeout = setTimeout(function () {
            displayPopinAideGlobal();
        }, 3000);

        var loadHandler = function (e) {
            jQuery('#contenu-fond-image img').off('load', loadHandler);

            clearTimeout(popinAideGlobalTimeout);
            popinAideGlobalTimeout = setTimeout(function () {
                displayPopinAideGlobal();
            }, 1000);

        }
        jQuery('#contenu-fond-image img').on('load', loadHandler);
    }

    // Désactivation du clic droit si on est pas en développement
    if (!jQuery('body').hasClass('dev')) {
        jQuery(document).bind("contextmenu", function (e) {
//            event.preventDefault();
        });
    }

    jQuery('body')
        .on('click', '#menu-lien-liste-selection', function (e) {
            toggleMaSelection();

            e.preventDefault();
        })
        .on('click', '#popin-selection-entite-erreur-maximum-contenu #confirmation-oui', function (e) {
            e.preventDefault();

            jQuery('#menu-bloc-actions-enregistrer a').trigger('click');

            jQuery('#popin-choix-offre').fadeIn();
        })
        .on('click', '#popin-choix-offre .button-close-popin-choice-offer', function (e) {
            e.preventDefault();

            jQuery('#popin-choix-offre').fadeOut();
        })
        .on('click', '#popin-selection-choix-offre .modal-table-offres .bouton button', function () {
            let alreadyActive = jQuery(this).hasClass('active');
            jQuery('.modal-table-offres .bouton button').removeClass('active');
            if (alreadyActive)
                jQuery('#mjmt_front_inscription_project_offre_1').prop('checked', true);
            else
                jQuery(this).addClass('active').closest('.une-offre').find('.radio input[type="radio"]').prop('checked', true);

            jQuery('.button-help-choice-offer').addClass('d-none');
            jQuery('.button-validation').removeAttr('disabled');

            return false;
        });

    if (jQuery('#conteneur-entites .selection-entite').length > 0) {
        toggleMaSelection();
        updateTotalPrice();
        updateNumberSelection();
    }
});
