function initialiserContenu(idEntite) {
    terminerContenu();

    jQuery('#contenu div.entite-accroche').removeClass('entite-en-arriere').addClass('entite-en-arriere');
    jQuery('#contenu div#entite-photo-' + idEntite).removeClass('entite-en-arriere');
    jQuery('#contenu div#entite-photo-' + idEntite).addClass('entite-en-edition');
    jQuery('#actions #actions-titre').html(jQuery('#maSelection-entite-' + idEntite + ' .entite-infos h2').html());
    jQuery('#actions').css('display', 'block');

    placerProportionHomme(idEntite);
    modifierActionEntite(idEntite);
}

function placerProportionHomme(idEntite) {

    if (!jQuery('#contenu div.rendu-proportion-homme').hasClass('visible'))
        jQuery('#contenu div.rendu-proportion-homme').addClass('visible');

    var entiteHtml = recupererEntite(idEntite);
    var left = entiteHtml.position().left + entiteHtml.outerWidth() + 10;
    var bottom = jQuery('#contenu').height() - (entiteHtml.position().top + entiteHtml.outerHeight() + 10); // 10 pixels pour l'ombre des pieds
    var zIndex = entiteHtml.attr('attr-zindex');

    var topEntite = entiteHtml.position().top;
    var leftEntite = entiteHtml.position().left;
    var entityImageHeight = jQuery('#contenu .rendu-proportion-homme img').attr('attr-height');
    var entityImageWidth = jQuery('#contenu .rendu-proportion-homme img').attr('attr-width');
    var entiteContenuHtml = recupererEntiteImage(idEntite);

    var xO = leftEntite + (entiteContenuHtml.width() / 2);
    var yO = topEntite + entiteHtml.height();

    var taille = recupererTailleEntite(xO, yO, 170, entityImageWidth, entityImageHeight);

    if ((left + taille['width']) > jQuery('#contenu').width()) {
        left = leftEntite - 10 - taille['width'];
    }

    jQuery('#contenu .rendu-proportion-homme').css({left: left, bottom: bottom, zIndex: zIndex});
    jQuery('#contenu .rendu-proportion-homme img').css({width: taille['width'], height: taille['height']});
}

function initialiserTailleClicDeselectionEntite() {
    // On fixe la taille de l'élement qui permet de déselectionner une entité
    jQuery('#trigger-click-outside-entity').height(jQuery(document).outerHeight());
}

function initialiserEntites() {
    if (jQuery('#contenu div.entite-accroche').length > 0) {
        var repereMin = recupererPlusPetitRepere();
        var repereMax = recupererPlusGrandRepere();

        jQuery('#contenu div.entite-accroche').each(function () {
            var top = parseFloat(jQuery(this).attr('attr-top'));
            var left = parseFloat(jQuery(this).attr('attr-left'));

            var hidden = false;
            if(jQuery(this).attr('attr-visible') == 0) {
                hidden = true;
            }

            //display: (jQuery(this).attr('attr-visible') == '0' ? 'none' : 'block'),
            jQuery(this).css({
                zIndex: jQuery(this).attr('attr-zindex'),
                top: top + 'px',
                left: left + 'px',
                display: 'block'
            });
            var posNoRot = {top: top, left: left};

            jQuery(this).css('transform', 'rotate(' + jQuery(this).attr('attr-rotation') + 'deg)');
            var posRot = jQuery(this).position();
            var dY = posRot.top - posNoRot.top;
            var dX = posRot.left - posNoRot.left;

            jQuery(this).css({
                top: (top - dY) + 'px',
                left: (left - dX) + 'px',
            });
            
            jQuery(this).find('div[id^="entite-photo-position"]').css('transform', jQuery(this).attr('attr-transformation'));
            jQuery(this).find('img').css({
                width: jQuery(this).attr('attr-adaptive-width') + 'px',
                height: jQuery(this).attr('attr-adaptive-height') + 'px'
            });

            jQuery(this).find('div').width(jQuery(this).find('img').outerWidth());
            if(hidden === true) {
                jQuery(this).hide();
            }

            var img = new Image();
            var img2 = jQuery(this);
            img.onload = function () {
                img2.attr('attr-resize-width', img.width);
                img2.attr('attr-resize-height', img.height);
                delete img;
            }
            img.src = jQuery(this).find('img').attr('src');

            var tailleMin = recupererTailleEntite(repereMin['x'], repereMin['y'], jQuery(this).attr('attr-entity-height'), jQuery(this).attr('attr-width'), jQuery(this).attr('attr-height'));
            var tailleMax = recupererTailleEntite(repereMax['x'], repereMax['y'], jQuery(this).attr('attr-entity-height'), jQuery(this).attr('attr-width'), jQuery(this).attr('attr-height'));

            jQuery(this).attr('attr-resize-min-height', tailleMin['height']);
            jQuery(this).attr('attr-resize-max-height', tailleMax['height']);
        });
    }
}

function recupererEntiteIdContenuApresClic(divId) {
    var idEntite = divId.split('-');
    idEntite = idEntite[2] + '-' + idEntite[3];
    return idEntite;
}

function terminerContenu() {
    jQuery('#contenu div.entite-accroche').removeClass('entite-en-edition entite-en-arriere');
    jQuery('#contenu div.rendu-proportion-homme').removeClass('visible');
    jQuery('#actions').css('display', 'none');
}

function ajouterEntiteContenu(html, redimensionner) {
    if (typeof redimensionner == "undefined" || redimensionner !== false) {
        redimensionner = true;
    }

    var id = jQuery(html).attr('id');
    var zindex = trouverZindexPlusGrand();
    var repereMin = recupererPlusPetitRepere();
    var repereMax = recupererPlusGrandRepere();

    jQuery('#contenu').append(html);
    jQuery('#' + id).zIndex(zindex + 1);
    jQuery('#' + id).attr('attr-zindex', (zindex + 1));
    centrerDivAvecParent(id, 'contenu');

    var img = new Image();
    img.onload = function () {
        jQuery('#' + id).attr('attr-resize-width', img.width);
        jQuery('#' + id).attr('attr-resize-height', img.height);
        delete img;
    }
    img.src = jQuery('#' + id + ' img').attr('src');

    var tailleMin = recupererTailleEntite(repereMin['x'], repereMin['y'], jQuery('#' + id).attr('attr-entity-height'), jQuery('#' + id).attr('attr-width'), jQuery('#' + id).attr('attr-height'));
    var tailleMax = recupererTailleEntite(repereMax['x'], repereMax['y'], jQuery('#' + id).attr('attr-entity-height'), jQuery('#' + id).attr('attr-width'), jQuery('#' + id).attr('attr-height'));

    jQuery('#' + id).attr('attr-resize-min-height', tailleMin['height']);
    jQuery('#' + id).attr('attr-resize-max-height', tailleMax['height']);

    if (redimensionner) {
        redimensionnerAutoEntite(recupererEntiteIdContenuApresClic(id));
        redimensionnerAutoEntite(recupererEntiteIdContenuApresClic(id));
    }


    //jQuery('#' + id).find('div').width(jQuery('#' + id).find('img').outerWidth());
    jQuery('#' + id).find('div').height(jQuery('#' + id).find('img').outerHeight());

    centrerDivAvecParent(id, 'contenu');
    return id;
}

function trouverZindexLasso() {
    var zIndex = 0;
    jQuery('#contenu div.entite-accroche').each(function () {
        if (typeof jQuery(this).attr('attr-lasso') != "undefined" && jQuery(this).attr('attr-lasso') == 1) {
            if (jQuery(this).zIndex() > zIndex) {
                zIndex = jQuery(this).zIndex();
            }
        }
    });

    return zIndex + 1;
}


function trouverZindexPlusGrand() {
    var zindex = 20;
    jQuery('#contenu div.entite-accroche').each(function () {
        if (jQuery(this).zIndex() > zindex)
            zindex = jQuery(this).zIndex();
    });
    return zindex;
}

function recalculerZindex() {
    /*
     * la fonction est inutile et provoque un problème avec le lasso
     * var zindex = [];
    jQuery('#contenu div.entite-accroche').each(function() {
        zindex[jQuery(this).zIndex()] = jQuery(this).attr('id');
    });
    
    var i = 1;
    for (var zi in zindex) {
        jQuery('#' + zindex[zi]).zIndex(i);
        jQuery('#' + zindex[zi]).attr('attr-zindex', i);
        i++;
    }
     */
}

function nombreEntiteContenu() {
    return jQuery('#contenu div.entite-accroche').length;
}

/*
function creationCanvasGlobalPourImage() {
    
    //Création du canvas global
    var canvas = document.createElement('canvas');
    canvas.width = jQuery('#contenu-fond-image img').width();
    canvas.height = jQuery('#contenu-fond-image img').height();
    canvas.getContext('2d').drawImage(jQuery('#contenu-fond-image img').get(0), 0, 0, canvas.width, canvas.height);
    
    //On parcours les entites et on les place dans le canvas global (attention image ou canvas)
    jQuery('#contenu div.entite-accroche').each(function() {
        var idEntite = jQuery(this).attr('attr-creation-entite-id') + '-' + jQuery(this).attr('attr-date-ajout');
        //var entiteHtml = recupererEntite(idEntite);
        
    });
    var image = recupererEntiteImage(idEntite).get(0);
}
*/

function contenuMouseEnter() {
    // Surbrillance de l'image dans l'espace de travail quand on passe la souris dessus dans le contenu
    jQuery('body').on({
        mouseenter: function () {
            var idEntite = recupererEntiteIdContenuApresClic(jQuery(this).attr('id'));
            jQuery('#entite-photo-' + idEntite).addClass('entite-surbrillance');
        },
        mouseleave: function () {
            var idEntite = recupererEntiteIdContenuApresClic(jQuery(this).attr('id'));
            jQuery('#entite-photo-' + idEntite).removeClass('entite-surbrillance');
        }
    }, 'div.entite-accroche');
}

function contenuMouseDown() {
    // Le centrage du background se fait lors des resizes
    jQuery('#contenu').on('mousedown', 'div.entite-accroche', function (e) {
        var idEntite = recupererEntiteIdContenuApresClic(jQuery(this).attr('id'));

        if (!jQuery(this).hasClass('entite-en-edition') && recupererEntiteIdCourante() != idEntite) {
            if (clickEntiteMaSelection(idEntite, true)) {
                recalculerZindex();
                jQuery(this).trigger(e);
            }

        } else if (!estAValider() && recupererActionActive() != 'action-deplacer' && recupererActionActive() != 'action-gomme' && recupererActionActive() != 'action-rotation' && recupererActionActive() != 'action-transformation') {
            if (recupererEntiteIdCourante() != idEntite) {
                if (clickEntiteMaSelection(idEntite, true)) {
                    jQuery(this).trigger(e);
                }
            } else if (recupererEntiteIdCourante() == idEntite) {
                deplacerEntite(idEntite);
                jQuery(this).trigger(e);
            }

        }
    });
}

jQuery(document).ready(function () {
    //  On initialise lors de la première fois 
    initialiserEntites();

    // Centrage du background quand l'image est finie d'être chargée
    jQuery('#contenu').css({
        width: jQuery('#contenu-fond-image img').width() + 'px',
        height: jQuery('#contenu-fond-image img').height() + 'px'
    });
//    jQuery('body').css({minWidth: (jQuery('#contenu-fond-image img').width() + 168) + 'px', minHeight: (jQuery('#contenu-fond-image img').height() + 112) + 'px'});
//    centrerDiv('contenu');
    // Affichage l'interface du tampon
    jQuery('body').on('click', '#menu-tampon', function (e) {
        menuItemClickedEvent(e).then(function (shouldContinue) {
            if (shouldContinue == true) {
                initialiserTampon();
            }
        });

        return false;
    });

    jQuery('body').on('click', '#menu-rotation', function (e) {
        menuItemClickedEvent(e).then(function (shouldContinue) {
            if (shouldContinue == true) {
                jQuery('#menu-bloc-actions').trigger('menu-item-clicked', [e]);
                initialiserRotation();
            }
        });

        return false;
    });

    // Le centrage du background se fait lors des resizes
    jQuery(window).resize(function () {
//        centrerDiv('contenu');
        initialiserTailleClicDeselectionEntite();
        centrerFenetreActions();
        centrerImageFond();
    });

    // On fixe la taille de l'élement qui permet
    initialiserTailleClicDeselectionEntite();

    // On déselectionne l'image en cours
    jQuery('#trigger-click-outside-entity,#contenu-fond-image').click(function (event) {
        //event.preventDefault();

        if (!debloquerAction())
            return false;

        terminerAction();
        terminerContenu();
        terminerMaSelection();
    });


    contenuMouseDown();
    contenuMouseEnter();


    /* Fenetre de fermeture (confirmation) */
    /*$(window).on('beforeunload', function(){
        //sauvegarderCreation(jQuery('#menu-sauvegarder-creation a').attr('href'), false, false, false);
        return "Vous n'avez peut être pas enregistré vos modifications.";
      });*/

    centrerFenetreActions();
    centrerImageFond();
});

function centrerFenetreActions() {
    var windowWidth = jQuery(window).width();
    var menuWidth = jQuery('#menu').width();
    var actionsWidth = jQuery('#actions').width();
    // var newMargin = (parseFloat(menuWidth) + parseFloat(((windowWidth - menuWidth) / 2) - (actionsWidth / 2)));
    var newMargin = parseFloat((windowWidth - actionsWidth) / 2) + parseFloat(menuWidth / 2);

    if (newMargin >= menuWidth)
        jQuery('#actions').css('margin-left', newMargin + 'px');
}

function centrerImageFond() {
    var windowWidth = jQuery(window).width();
    var menuWidth = jQuery('#menu').width();
    var contenuWidth = jQuery('#contenu').width();
    // var newMargin = (parseFloat(menuWidth) + parseFloat(((windowWidth - menuWidth) / 2) - (contenuWidth / 2)));
    var newMargin = parseFloat((windowWidth - contenuWidth) / 2) + parseFloat(menuWidth / 2);

    if (newMargin >= menuWidth)
        jQuery('#contenu').css('margin-left', newMargin + 'px');
}
