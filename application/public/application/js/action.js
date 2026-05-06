function recupererEntiteIdCourante() {
    return jQuery('input#action-entite-id').val();
}

function recupererEntite(idEntite) {
    return jQuery('#entite-photo-' + idEntite);
}

function recupererEntitePosition(idEntite) {
    return jQuery('#entite-photo-' + idEntite).find('div[id^="entite-photo-position"]');
}

function recupererEntiteClone() {
    return jQuery('#contenu-clone').find('img').length > 0 ? jQuery('#contenu-clone').find('img') : jQuery('#contenu-clone').find('canvas');
    ;
}

function recupererTypeEntiteClone() {
    return jQuery('#contenu-clone').find('img').length > 0 ? 'img' : 'canvas';
}

function supprimerEntiteClone() {
    jQuery('#contenu-clone').html('');
    initialiserTailleClicDeselectionEntite();
    updateTotalPrixSelection();
}

function recupererEntiteImage(idEntite) {
    return recupererEntite(idEntite).find('img.entite').length > 0 ? recupererEntite(idEntite).find('img.entite') : recupererEntite(idEntite).find('canvas');
}

function recupererTypeEntite(idEntite) {
    return recupererEntite(idEntite).find('img.entite').length > 0 ? 'img' : 'canvas';
}

function masquerEntites() {
    jQuery('.entite-accroche').css('display', 'none');
}

function afficherEntites() {
    jQuery('.entite-accroche').css('display', 'block');
}

function transformerEntiteEnCanvas(idEntite) {
    var canvas = null;
    if (recupererTypeEntite(idEntite) == 'img') {
        canvas = document.createElement("canvas");
        var image = recupererEntiteImage(idEntite).get(0);
        canvas.width = recupererEntite(idEntite).attr('attr-resize-width');
        canvas.height = recupererEntite(idEntite).attr('attr-resize-height');
        canvas.getContext("2d").drawImage(image, 0, 0, recupererEntite(idEntite).attr('attr-resize-width'), recupererEntite(idEntite).attr('attr-resize-height'));
        jQuery(canvas).width(image.width);
        jQuery(canvas).height(image.height);
        recupererEntiteImage(idEntite).remove();
        recupererEntite(idEntite).find('div').append(canvas);
    }
    return canvas;
}

function recupererEntiteMS(idEntite) {
    return jQuery('#maSelection-entite-' + idEntite);
}

var idEntiteEnAttenteSuppression = null;
var isErasing = false;

function suppressionEntite(idEntite, force) {
    if (!debloquerAction())
        return false;

    if (!force) {
        idEntiteEnAttenteSuppression = idEntite;
        toggleConfirmation();

        jQuery('#popin-confirmation #popin-confirmation-ko').click(function () {
            toggleConfirmation();
            jQuery(this).unbind("click");
            jQuery('#popin-confirmation #popin-confirmation-ok').unbind("click");
            idEntiteEnAttenteSuppression = null;
        });

        jQuery('#popin-confirmation #popin-confirmation-ok').click(function () {
            toggleConfirmation();
            jQuery(this).unbind("click");
            jQuery('#popin-confirmation #popin-confirmation-ko').unbind("click");
            suppressionEntite(idEntiteEnAttenteSuppression, true);
        });
    } else {
        var entiteContenuHtml = recupererEntite(idEntite);
        var entiteMaSelection = recupererEntiteMS(idEntite);
        entiteMaSelection.remove();
        entiteContenuHtml.remove();
        if (recupererEntiteIdCourante() == idEntite) {
            terminerAction();
            terminerContenu();
        }
        if (!isTouchDevice())
            jQuery('.tipsy').remove();
        recalculerZindex();
        idEntiteEnAttenteSuppression = null;

        var idRemove = idEntite.split('-');
        jQuery('#dernier-article-ajout-' + idRemove[0]).remove();

//        if (jQuery('#conteneur-entites .selection-entite').length === 0) {
//            toggleMaSelection();
//        }

        updateTotalPrice();
        updateNumberSelection();
    }

    return true;
}

function visibiliteEntite(idEntite) {
    if (!debloquerAction())
        return false;

    var entiteContenuHtml = recupererEntite(idEntite);
    if (entiteContenuHtml.css('display') !== 'none') {
        entiteContenuHtml.css('display', 'none');
        entiteContenuHtml.attr('attr-visible', '0');
    } else {
        entiteContenuHtml.css('display', 'block');
        entiteContenuHtml.attr('attr-visible', '1');
    }
}

function symetrieEntite(idEntite) {
    if (!debloquerAction())
        return false;

    transformerEntiteEnCanvas(idEntite);

    Pixastic.process(recupererEntiteImage(idEntite).get(0), "fliph");
    recupererEntite(idEntite).attr('attr-symetrie', (recupererEntite(idEntite).attr('attr-symetrie') == '1' ? '0' : '1'));

    return true;
}

function agrandirEntite(idEntite) {
    var $entiteHtml = recupererEntite(idEntite);
    var $entiteImage = recupererEntiteImage(idEntite);
    var posLeft = $entiteHtml.css('left');
    var posTop = $entiteHtml.css('top');

    if(typeof($entiteHtml.attr('resize-pos-left')) == "undefined" || typeof($entiteHtml.attr('resize-pos-top')) == "undefined"
        || posLeft != $entiteHtml.attr('resize-pos-left') || posTop != $entiteHtml.attr('resize-pos-top')) {
        $entiteHtml.attr('resize-image-original-height', $entiteImage.height());
    }

    $entiteHtml.attr('resize-pos-left', posLeft);
    $entiteHtml.attr('resize-pos-top', posTop);
    return redimensionnerEntite(idEntite, 2, posLeft, posTop);
}

function diminuerEntite(idEntite) {var $entiteHtml = recupererEntite(idEntite);
    var $entiteHtml = recupererEntite(idEntite);
    var $entiteImage = recupererEntiteImage(idEntite);
    var posLeft = $entiteHtml.css('left');
    var posTop = $entiteHtml.css('top');

    if(typeof($entiteHtml.attr('resize-pos-left')) == "undefined" || typeof($entiteHtml.attr('resize-pos-top')) == "undefined"
        || posLeft != $entiteHtml.attr('resize-pos-left') || posTop != $entiteHtml.attr('resize-pos-top')) {
        $entiteHtml.attr('resize-image-original-height', $entiteImage.height());
    }

    $entiteHtml.attr('resize-pos-left', $entiteHtml.css('left'));
    $entiteHtml.attr('resize-pos-top', $entiteHtml.css('top'));
    return redimensionnerEntite(idEntite, -2, posLeft, posTop);
}

function _repositionRotatableHandle(handlePossiblePosition, handleHeight, handleWidth, $rotatableHandle, $img, x, y) {
    var success = false;

    if(handlePossiblePosition == 'bottom') {        
        if(y + handleHeight <= jQuery('#contenu-fond-image').height() && (x + ($img.width() / 2) + handleWidth <= jQuery('#contenu-fond-image').width()) ) {
            var positionTop = ($rotatableHandle.position().top + $img.height()) + handleHeight + 10;
            $rotatableHandle.css('top', positionTop);
            success = true;
        }
    } else if(handlePossiblePosition == 'left') {
        if(x + handleWidth <= jQuery('#contenu-fond-image').width() && (y + ($img.height() / 2) + handleHeight <= jQuery('#contenu-fond-image').height()) ) {
            var positionLeft = ($rotatableHandle.position().left + $img.width()) + handleWidth + 10;
            $rotatableHandle.css('left', positionLeft);
            success = true;
        }
    } else if(handlePossiblePosition == 'right') {
        if(x - handleWidth <= 0 && (y + ($img.height() / 2) + handleHeight <= jQuery('#contenu-fond-image').height()) ) {
            var positionRight = ($rotatableHandle.position().right - $img.width()) + handleWidth + 10;
            $rotatableHandle.css('left', positionRight);
            success = true;
        }
    }

    return success;
}

function rotationEntite(idEntite) {
    if (!debloquerAction())
        return false;

    bloquerAction('action-rotation');

    var entiteHtml = recupererEntite(idEntite);
    var entiteHtmlImage = recupererEntiteImage(idEntite);

    if (!entiteHtml.hasClass('ui-rotatable')) {
        arreterDeplacerEntite(idEntite);
        entiteHtml.rotatable({
            angle: entiteHtml.attr('attr-rotation'),
            handlePlacedCallback: function(element){
                setTimeout(function(){
                    var $img = jQuery(element).find('img').length > 0 ? jQuery(element).find('img') : jQuery(element).find('canvas');
                    var x = jQuery(element).position().left;
                    var y = jQuery(element).position().top - ($img.height() - jQuery(element).height());

                    var $rotatableHandle = jQuery(element).find('.ui-rotatable-handle');
                    var handleWidth = jQuery($rotatableHandle).width();
                    var handleHeight = jQuery($rotatableHandle).width();

                    var needToRepositionHandle = false;
                    if(y - handleHeight <= 0) {
                        needToRepositionHandle = true;
                    }

                    var HandlePossiblePositions = ['bottom', 'left', 'right'];
                    if(needToRepositionHandle) {
                        for (var i=0; i<HandlePossiblePositions.length; i++) {
                           var repositionSuccess = _repositionRotatableHandle(HandlePossiblePositions[i], handleHeight, handleWidth, $rotatableHandle, $img, x, y);
                           if(repositionSuccess === true) {
                               return true;
                           }
                        }
                    }
                }, 0)
                
            },
            stop: function (event, ui) {
                entiteHtml.attr('attr-rotation', ui.angle.stop);
                return true;
            }
        });
        entiteHtml.find('.ui-rotatable-handle').addClass('ui-rotatable-handle-top-left');
        var top = (-(entiteHtmlImage.height() - entiteHtml.height())) - 40;
        var left = (entiteHtmlImage.width() / 2) - 12;
        entiteHtml.find('.ui-rotatable-handle').css('top', top + 'px');
        entiteHtml.find('.ui-rotatable-handle').css('left', left + 'px');

    } else {
        arreterRotation(idEntite);
        deplacerEntite(idEntite);
    }

    return true;
}

function arreterRotation(idEntite) {
    var entiteHtml = recupererEntite(idEntite);
    if (entiteHtml.hasClass('ui-rotatable')) {
        entiteHtml.rotatable('destroy');
    }

    return true;
}

function transformationEntite(idEntite) {
    if (!debloquerAction())
        return false;

    jQuery('#actions').animate({
        'height': '140px'
    });

    bloquerAction('action-transformation');
    initialiserActionParametrage('indication-perspective');

    arreterDeplacerEntite(idEntite);
    var entiteHtml = recupererEntite(idEntite);
    var entiteHtmlImage = recupererEntiteImage(idEntite);

    if (!entiteHtml.hasClass('ui-transformation')) {
        entiteHtml.addClass('ui-transformation');
        var entiteHtmlPosition = recupererEntitePosition(idEntite);
        var haut = entiteHtml.height() - entiteHtmlImage.height() - 10;
        var milieuHaut = (entiteHtmlImage.width() / 2) - 3;
        var milieuGauche = haut + (entiteHtmlImage.height() / 2);
        var gauche = entiteHtmlImage.width() - 3;
        var centreX = parseFloat(entiteHtml.css('left').replace('px', '')) + (entiteHtml.width() / 2);
        var centreY = parseFloat(entiteHtml.css('top').replace('px', '')) + (entiteHtml.height() / 2) + haut;
        entiteHtml.css('perspective', '600px');

        entiteHtmlPosition.append('<div id="transformation-haut-gauche" class="transformation-rect transformation-rotate"style="top: ' + haut + 'px;"></div>');
        entiteHtmlPosition.append('<div id="transformation-haut-droit" class="transformation-rect transformation-rotate" style="left: ' + gauche + 'px; top: ' + haut + 'px"></div>');
        entiteHtmlPosition.append('<div id="transformation-bas-droit" class="transformation-rect transformation-rotate" style="left: ' + gauche + 'px;"></div>');
        entiteHtmlPosition.append('<div id="transformation-bas-gauche" class="transformation-rect transformation-rotate"></div>');

        entiteHtmlPosition.append('<div id="transformation-haut-milieu" class="transformation-rect transformation-skewx" style="left: ' + milieuHaut + 'px;top: ' + haut + 'px"></div>');
        entiteHtmlPosition.append('<div id="transformation-gauche-milieu" class="transformation-rect transformation-skewy" style="top: ' + milieuGauche + 'px;"></div>');

        var drag = null;
        var type = null;
        jQuery('.transformation-rect').mousedown(function (event) {
            event.preventDefault();
            drag = true;
            if (jQuery(this).hasClass('transformation-rotate'))
                type = 1;
            else if (jQuery(this).hasClass('transformation-skewx'))
                type = 2;
            else if (jQuery(this).hasClass('transformation-skewy'))
                type = 3;
        });
        jQuery('#contenu').mousemove(function (event) {
            event.preventDefault();
            if (drag == null) {
                return;
            }
            var positionSouris = {
                x: window.event.pageX - (jQuery('#contenu').position().left + parseFloat(jQuery('#contenu').css('marginLeft').replace('px', ''))),
                y: window.event.pageY - (jQuery('#contenu').position().top + parseFloat(jQuery('#contenu').css('marginTop').replace('px', '')))
            };

            var style = entiteHtmlPosition.attr('style');

            var matches = style != null ? style.match(/skewX\(([\-0-9\.]+)deg\)/) : null;
            var skewX = matches != null && matches.length > 0 ? matches[1] : 0;
            matches = style != null ? style.match(/skewY\(([\-0-9\.]+)deg\)/) : null;
            var skewY = matches != null && matches.length > 0 ? matches[1] : 0;
            matches = style != null ? style.match(/rotateX\(([\-0-9\.]+)deg\)/) : null;
            var rotateX = matches != null && matches.length > 0 ? matches[1] : 0;
            matches = style != null ? style.match(/rotateY\(([\-0-9\.]+)deg\)/) : null;
            var rotateY = matches != null && matches.length > 0 ? matches[1] : 0;

            switch (type) {
                case 1:
                    var distanceHypotenuse = (Math.pow(centreX - positionSouris.x, 2) + Math.pow(centreY - positionSouris.y, 2)) * 72;
                    var distanceOpposite = Math.pow(centreX - positionSouris.x, 2) * 72;
                    rotateY = ((Math.asin(distanceOpposite / distanceHypotenuse) * 180) / Math.PI);
                    if (centreX < positionSouris.x)
                        rotateY = -rotateY;
                    break;
                case 2:
                    var distanceHypotenuse = (Math.pow(centreX - positionSouris.x, 2) + Math.pow(centreY - positionSouris.y, 2)) * 72;
                    var distanceOpposite = Math.pow(centreX - positionSouris.x, 2) * 72;
                    skewX = ((Math.asin(distanceOpposite / distanceHypotenuse) * 180) / Math.PI);
                    if (centreX < positionSouris.x)
                        skewX = -skewX;
                    break;
                case 3:
                    var distanceHypotenuse = (Math.pow(centreX - positionSouris.x, 2) + Math.pow(centreY - positionSouris.y, 2)) * 72;
                    var distanceOpposite = Math.pow(centreY - positionSouris.y, 2) * 72;
                    skewY = ((Math.asin(distanceOpposite / distanceHypotenuse) * 180) / Math.PI);
                    if (centreY < positionSouris.y)
                        skewY = -skewY;
                    break;
            }

            var tranform = (skewY != 0 ? 'skewY(' + skewY + 'deg) ' : '') + (skewX != 0 ? 'skewX(' + skewX + 'deg) ' : '') + (rotateX != 0 ? 'rotateX(' + rotateX + 'deg) ' : '') + (rotateY != 0 ? 'rotateY(' + rotateY + 'deg)' : '');
            entiteHtmlPosition.css('transform', tranform);
            entiteHtml.attr('attr-transformation', tranform);
        });
        jQuery('#contenu').mouseup(function (event) {
            event.preventDefault();
            drag = null;
            type = null;
        });
        jQuery('#contenu').mouseleave(function (event) {
            event.preventDefault();
        });

    } else {
        arreterTransformation(idEntite);
        deplacerEntite(idEntite);
    }

    return true;
}

function arreterTransformation(idEntite) {

    if (recupererActionActive() == 'action-transformation') {

        if (jQuery('#actions').height() > 103) {
            jQuery('#actions').height(103);
        }

        var entiteHtml = recupererEntite(idEntite);
        var entiteHtmlPosition = recupererEntitePosition(idEntite);
        entiteHtml.removeClass('ui-transformation');
        entiteHtmlPosition.find('#transformation-haut-gauche').remove();
        entiteHtmlPosition.find('#transformation-haut-droit').remove();
        entiteHtmlPosition.find('#transformation-bas-droit').remove();
        entiteHtmlPosition.find('#transformation-bas-gauche').remove();
        entiteHtmlPosition.find('#transformation-haut-milieu').remove();
        entiteHtmlPosition.find('#transformation-gauche-milieu').remove();
        jQuery('#contenu').off('mouseup');
        jQuery('#contenu').off('mouseout');
        jQuery('#contenu').off('mousemove');
    }

    return true;
}

function redimensionnerEntite(idEntite, valeur, posLeft, posTop) {
    if (!debloquerAction())
        return false;

    var entiteHtml = recupererEntite(idEntite);
    var entiteContenuHtml = recupererEntiteImage(idEntite);
    var oldHeight = entiteContenuHtml.height();
    var height = Math.ceil(entiteContenuHtml.height() + valeur);
    var minHeight = parseFloat(entiteHtml.attr('attr-resize-min-height'));
    var maxHeight = parseFloat(entiteHtml.attr('attr-resize-max-height'));
    var posLeft = typeof(posLeft) == "undefined" ? null : posLeft;
    var posTop = typeof(posTop) == "undefined" ? null : posTop;

    if (height < minHeight)
        height = minHeight;
    else if (height > maxHeight)
        height = maxHeight;

    var tailleContenuHtml = jQuery('#contenu').height();
    var dimensionEntiteCm = entiteHtml.attr('attr-entity-height');

    var dimensionPersonnage = entiteHtml.closest('#contenu').find('.rendu-proportion-homme').height();
    var dimensionPersonnageCm = 170;
    var ratioCmPx = dimensionPersonnage / dimensionPersonnageCm;

    var toleranceAgrandissement = 0;
    if(tailleContenuHtml / (ratioCmPx * dimensionEntiteCm) >= 3) {
        toleranceAgrandissement = (ratioCmPx * dimensionEntiteCm)  / 2;
    }

    var samePos = false;
    if(posLeft !== null && posTop !== null && posLeft == entiteHtml.css('left') && posTop == entiteHtml.css('top')){
        samePos = true;
    }

    /*
    var imageOriginalHeight = parseFloat(entiteHtml.attr('resize-image-original-height'));
    if(!isNaN(imageOriginalHeight)){
        var thresholdAugmentation = (ratioCmPx * dimensionEntiteCm) + toleranceAgrandissement;

        if(thresholdAugmentation < imageOriginalHeight + (imageOriginalHeight * 0.2)) {
            thresholdAugmentation = imageOriginalHeight + (imageOriginalHeight * 0.2);
        }

        if(height > thresholdAugmentation  && height < thresholdAugmentation + 5 && samePos === true) {
            height = thresholdAugmentation;
        }


        //if((height > (ratioCmPx * dimensionEntiteCm) + toleranceAgrandissement) && height < thresholdAugmentation && samePos === true){
            //height = (ratioCmPx * dimensionEntiteCm) + toleranceAgrandissement;
        //}


        var thresholdDiminution = thresholdAugmentation - imageOriginalHeight;
        if(thresholdDiminution < imageOriginalHeight * 0.2) {
            thresholdDiminution = imageOriginalHeight * 0.2;
        }

        if(height < imageOriginalHeight - thresholdDiminution) {
            height = Math.floor(imageOriginalHeight - thresholdDiminution);
        }
    }
     */

    var pourcentage = (height * 100) / entiteHtml.attr('attr-height');
    var width = Math.ceil((entiteHtml.attr('attr-width') * pourcentage) / 100);

    entiteContenuHtml.width(width);
    entiteContenuHtml.height(height);
    entiteHtml.find('div').width(entiteContenuHtml.outerWidth());
    entiteHtml.width(entiteContenuHtml.outerWidth());
    //entiteHtml.css('top', (entiteHtml.position().top - (height - oldHeight)) + 'px');
    entiteHtml.attr('attr-taille-fixe', '1');

    placerProportionHomme(idEntite);

    return true;
}

function recupererTailleEntite(xO, yO, entityHeight, entityImageWidth, entityImageHeight) {
    // Distance (pythagore) = a² + b² = c²
    // Hauteur objet en pixel sur bonhomme = (Hauteur de l'objet en cm * Hauteur du bonhonne en pixel) /  Hauteur du bonhomme
    // Hauteur objet en pixel = (E(1,n)(Hauteur de l'objet en cm * Hauteur du bonhonne en pixel) /  (Distanvce du bonhomme en pixel * Hauteur du bonhomme))) / (E(1,n) 1 / Distance du bonhomme)
    var sommeHauteurOPixel = 0;
    var sommeDistanceBPixel = 0;
    var height = 0;

    for (var i = 1; i <= 4; i++) {
        var xB = parseFloat(jQuery('input[name="repere' + i + '_x"]').val()) + (parseFloat(jQuery('input[name="repere' + i + '_largeur"]').val()) / 2);
        var yB = parseFloat(jQuery('input[name="repere' + i + '_y"]').val()) + parseFloat(jQuery('input[name="repere' + i + '_hauteur"]').val());
        var dB = Math.sqrt(Math.pow((xO - xB), 2) + Math.pow((yO - yB), 2));

        var dBBas = Math.abs(jQuery('#contenu-fond-image').height() - yB);
        var dOBas = Math.abs(jQuery('#contenu-fond-image').height() - yO);
        dB = dB * Math.abs(dBBas - dOBas);

        if (dB == 0) {
            height = ((entityHeight * parseFloat(jQuery('input[name="repere' + i + '_hauteur"]').val())) / 170);
            break;
        } else {
            sommeDistanceBPixel += 1 / dB;
            sommeHauteurOPixel += ((entityHeight * parseFloat(jQuery('input[name="repere' + i + '_hauteur"]').val())) / (dB * 170));
        }
    }

    if (height == 0)
        height = sommeHauteurOPixel / sommeDistanceBPixel;
    height = Math.ceil(height);

    var pourcentage = (height * 100) / entityImageHeight;
    var width = Math.ceil((entityImageWidth * pourcentage) / 100);

    return {'width': width, 'height': height};
}

function redimensionnerAutoEntite(idEntite) {
    var entiteHtml = recupererEntite(idEntite);
    if (entiteHtml.attr('attr-taille-fixe') == '0') {
        var top = entiteHtml.position().top;
        var left = entiteHtml.position().left;
        var entityHeight = parseFloat(entiteHtml.attr('attr-entity-height'));
        var entityImageHeight = parseFloat(entiteHtml.attr('attr-height'));
        var entityImageWidth = parseFloat(entiteHtml.attr('attr-width'));
        var entiteContenuHtml = recupererEntiteImage(idEntite);
        var entityImageOldHeight = entiteContenuHtml.height();
        var entityImageOldWidth = entiteContenuHtml.width();
        var positionTop = entiteHtml.position().top;
        var positionLeft = entiteHtml.position().left;
        var minHeight = parseFloat(entiteHtml.attr('attr-resize-min-height'));
        var maxHeight = parseFloat(entiteHtml.attr('attr-resize-max-height'));

        var xO = left + (entiteContenuHtml.width() / 2);
        var yO = top + entiteHtml.height();

        var taille = recupererTailleEntite(xO, yO, entityHeight, entityImageWidth, entityImageHeight);

        entiteContenuHtml.width(taille['width']);
        entiteContenuHtml.height(taille['height']);
        entiteHtml.width(entiteContenuHtml.outerWidth());
        entiteHtml.find('div').width(entiteContenuHtml.outerWidth());

        return true;
    } else {
        return false;
    }
}

function arriereEntite(idEntite) {
    if (!debloquerAction())
        return false;

    var entiteContenuHtml = recupererEntite(idEntite);
    var zindexActuel = entiteContenuHtml.zIndex();

    if (zindexActuel > 1) {
        jQuery('div#contenu div.entite-accroche').each(function (e) {
            if (jQuery(this).zIndex() == zindexActuel - 1) {
                jQuery(this).zIndex(zindexActuel);
                jQuery(this).attr('attr-zindex', zindexActuel);
            }
        });

        entiteContenuHtml.zIndex(zindexActuel - 1);
        entiteContenuHtml.attr('attr-zindex', zindexActuel - 1);

        recalculerZindex();

        return true;
    }

    return false;
}

function avantEntite(idEntite) {
    if (!debloquerAction())
        return false;

    var entiteContenuHtml = recupererEntite(idEntite);
    var zindexActuel = parseInt(entiteContenuHtml.css('zIndex'));

    jQuery('div#contenu div.entite-accroche').each(function (e) {
        if (jQuery(this).zIndex() == zindexActuel + 1) {
            jQuery(this).zIndex(zindexActuel);
            jQuery(this).attr('attr-zindex', zindexActuel);
        }
    });

    entiteContenuHtml.zIndex(zindexActuel + 1);
    entiteContenuHtml.attr('attr-zindex', zindexActuel + 1);

    recalculerZindex();

    return true;

    return false;
}

function changerTailleGomme(taille) {
    tailleGomme = taille;

    // On change la taille si l'action est en cours d'utilisation
    if (recupererActionActive() == 'action-gomme')
        jQuery('#entite-photo-' + recupererEntiteIdCourante() + ' div > *').eraser('size', tailleGomme);
}

function recupererTailleGomme() {
    return tailleGomme;
}

var demarrageGomme = true;

function gommerEntite(idEntite, forcer) {
    jQuery('#actions').animate({
        'height': '130px'
    });

    var ancienneAction = recupererActionActive();

    if (!debloquerAction())
        return false;

    if (ancienneAction != 'action-gomme') {
        masquerRotationComposition();
        bloquerAction('action-gomme');
        initialiserActionParametrage('taille-gomme');

        var entiteHtml = recupererEntite(recupererEntiteIdCourante());
        entiteHtml.css('transform', '');
        entiteHtml.find('div[id^="entite-photo-position"]').css('transform', '');
        entiteHtml.css('opacity', '0.5');

        recupererEntite(idEntite).zIndex(trouverZindexPlusGrand() + 1);
        transformerEntiteEnCanvas(idEntite);


        jQuery('#entite-photo-' + idEntite + ' div > *').eraser({size: recupererTailleGomme()});

        jQuery('#contenu').append('<div id="curseur-gomme"></div>');

        //Actions
        jQuery('#entite-photo-' + recupererEntiteIdCourante()).on('mouseenter', 'canvas', function (e) {
            demarrageGomme = true;
            document.getElementById('curseur-gomme').style.display = 'block';
            document.getElementById('curseur-gomme').style.left = (e.pageX - (recupererTailleGomme() / 2) - document.getElementById('contenu').offsetLeft) + 'px';
            document.getElementById('curseur-gomme').style.top = (e.pageY - (recupererTailleGomme() / 2) - document.getElementById('contenu').offsetTop) + 'px';
            document.getElementById('curseur-gomme').style.width = recupererTailleGomme() + 'px';
            document.getElementById('curseur-gomme').style.height = recupererTailleGomme() + 'px';
            document.getElementsByTagName("body")[0].style.cursor = 'none';

            jQuery('#curseur-gomme').bind('mousedown', function (e) {
                isErasing = true;
                jQuery('#entite-photo-' + recupererEntiteIdCourante() + ' canvas').eraser('mouseDown', e);

                jQuery('#curseur-gomme').bind('mousemove', function (e) {
                    jQuery('#entite-photo-' + recupererEntiteIdCourante() + ' canvas').eraser('mouseMove', e);
                });
            });

            jQuery('#curseur-gomme').bind('touchstart', function (e) {
                jQuery('#entite-photo-' + recupererEntiteIdCourante() + ' canvas').eraser('touchStart', e);
            });

            jQuery('#curseur-gomme').bind('touchmove', function (e) {
                jQuery('#entite-photo-' + recupererEntiteIdCourante() + ' canvas').eraser('touchMove', e);
            });

            jQuery('#curseur-gomme').bind('touchend', function (e) {
                jQuery('#entite-photo-' + recupererEntiteIdCourante() + ' canvas').eraser('touchEnd', e);
            });
        });

        jQuery('body').bind('mousemove', function (e) {
            document.getElementById('curseur-gomme').style.display = 'block';
            document.getElementsByTagName("body")[0].style.cursor = 'none';
            document.getElementById('curseur-gomme').style.left = (e.pageX - (recupererTailleGomme() / 2) - document.getElementById('contenu').offsetLeft - 2) + 'px';
            document.getElementById('curseur-gomme').style.top = (e.pageY - (recupererTailleGomme() / 2) - document.getElementById('contenu').offsetTop - 2) + 'px';
            var cursor_top = document.getElementById('curseur-gomme').offsetTop + 2 + (recupererTailleGomme() / 2);
            var cursor_left = document.getElementById('curseur-gomme').offsetLeft + 2 + (recupererTailleGomme() / 2);
            var entite_top = document.getElementById('entite-photo-' + recupererEntiteIdCourante()).offsetTop + (document.getElementById('entite-photo-' + recupererEntiteIdCourante()).offsetHeight - recupererEntiteImage(recupererEntiteIdCourante()).outerHeight());
            var entite_left = document.getElementById('entite-photo-' + recupererEntiteIdCourante()).offsetLeft;
            var entite_top_max = entite_top + recupererEntiteImage(recupererEntiteIdCourante()).outerHeight() + 2;
            var entite_left_max = entite_left + recupererEntiteImage(recupererEntiteIdCourante()).outerWidth() + 2;

            if (cursor_top < entite_top
                || cursor_left < entite_left
                || cursor_top > entite_top_max
                || cursor_left > entite_left_max) {
                demarrageGomme = false;
                document.getElementById('curseur-gomme').style.display = 'none';
                document.getElementsByTagName("body")[0].style.cursor = 'auto';

                jQuery(document).trigger('mouseup.eraser', e);
                jQuery('#entite-photo-' + recupererEntiteIdCourante() + ' canvas').eraser('touchEnd', e);

                jQuery('#curseur-gomme').unbind('mousedown');
                jQuery('#curseur-gomme').unbind('mousemove');
                jQuery('#curseur-gomme').unbind('touchstart');
                jQuery('#curseur-gomme').unbind('touchmove');
                jQuery('#curseur-gomme').unbind('touchend');
            } else if (!demarrageGomme) {
                jQuery('#entite-photo-' + recupererEntiteIdCourante() + ' canvas').trigger('mouseenter');
            }
        });


        return true;
    }
    return false;
}

// Arrête le fonctionnement du plugin Eraser pour les images
function arreterGommerEntite(idEntite) {
    if (recupererActionActive() == 'action-gomme') {
        jQuery('#actions').animate({
            'height': '93px'
        });

        var vieuxCanvas = recupererEntiteImage(idEntite);
        var nouveauCanvas = vieuxCanvas.clone();
        jQuery.each(vieuxCanvas, function (index, value) {
            if (vieuxCanvas[index]) {
                var originalContext = vieuxCanvas[index].getContext("2d");
                var imageData = originalContext.getImageData(0, 0, vieuxCanvas.width(), vieuxCanvas.height());
                var cloneContext = nouveauCanvas[index].getContext("2d");
                cloneContext.putImageData(imageData, 0, 0);
            }
        });

        jQuery(vieuxCanvas).remove();
        if (nouveauCanvas)
            recupererEntite(idEntite).find('div').html(nouveauCanvas);

        jQuery(nouveauCanvas).css('cursor', 'default');

        jQuery('#curseur-gomme').remove();
        jQuery('body').unbind('mousemove');
        jQuery('#entite-photo-' + recupererEntiteIdCourante()).unbind('mouseenter');
        jQuery('body').css('cursor', 'auto');

        var entiteHtml = recupererEntite(recupererEntiteIdCourante());
        entiteHtml.css('transform', 'rotate(' + entiteHtml.attr('attr-rotation') + 'deg)');
        entiteHtml.find('div[id^="entite-photo-position"]').css('transform', entiteHtml.attr('attr-transformation'));
        entiteHtml.css('opacity', '1');

        demarrageGomme = false;

        recupererEntite(idEntite).zIndex(recupererEntite(idEntite).attr('attr-zindex'));
    }
}

function cloneEntite(idEntite, direction) {
    var contenuACopier = direction == 'clone' ? recupererEntiteImage(idEntite) : recupererEntiteClone();
    var contenuReceveur = direction == 'clone' ? jQuery('#contenu-clone') : recupererEntite(idEntite).find('div');
    var contenuType = direction == 'clone' ? recupererTypeEntite(idEntite) : recupererTypeEntiteClone();

    if (contenuType == 'img')
        contenuReceveur.html(contenuACopier.clone());
    else if (contenuType == 'canvas') {
        var nouveauContenu = contenuACopier.clone();
        jQuery.each(contenuACopier, function (index, value) {
            if (contenuACopier[index]) {
                var originalContext = contenuACopier[index].getContext("2d");
                var imageData = originalContext.getImageData(0, 0, contenuACopier.width(), contenuACopier.height());
                var cloneContext = nouveauContenu[index].getContext("2d");
                cloneContext.putImageData(imageData, 0, 0);
            }
        });
        contenuReceveur.html(nouveauContenu);
    }
}

function validerEntite(idEntite) {
    supprimerEntiteClone();
    validerOkAction();
}

function annulerEntite(idEntite) {
//    jQuery('#entite-photo-' + idEntite + ' canvas').eraser('reset');
    cloneEntite(idEntite, 'entite');
    supprimerEntiteClone();
    validerOkAction();
}

// Arrête le fonctionnement du plugin Eraser pour les images
function arreterDeplacerEntite(idEntite) {
    if (recupererEntite(idEntite).is('.ui-draggable'))
        recupererEntite(idEntite).draggable('destroy');
}

function deplacerEntite(idEntite) {
    if (!debloquerAction())
        return false;

    bloquerAction('action-deplacer');
    recupererEntite(idEntite).draggable({
        drag: function (event, ui) {
            var id = recupererEntiteIdContenuApresClic(jQuery(this).attr('id'));

            // On force le positionnement de l'élément si on est en dehors de la zone (on ne peut pas passer par l'option containment
            // car la taille des entités varient avec le déplacement            
            if (ui.position.left < -(recupererEntite(id).width() - 20)) {
                ui.position.left = -(recupererEntite(id).width() - 20);
            } else if (ui.position.left > jQuery('#contenu').width() - 20) {
                ui.position.left = jQuery('#contenu').width() - 20;
            }
            if (ui.position.top < -(recupererEntite(id).height() - 20)) {
                ui.position.top = -(recupererEntite(id).height() - 20);
            } else if (ui.position.top > jQuery('#contenu').height() + 20) {
                ui.position.top = jQuery('#contenu').height() + 20;
            }

            redimensionnerAutoEntite(id);
            setTimeout(function () {
                placerProportionHomme(id);
            }, 5);
        }
    });
}

function deplacerXYEntite(idEntite, x, y) {

    var entite = recupererEntite(idEntite);
    entite.css({top: (entite.position().top + y) + 'px', left: (entite.position().left + x) + 'px'});
    redimensionnerAutoEntite(idEntite);
}

function deplacerVersXYEntite(idEntite, x, y, height, applyEntitePosition) {

    if (typeof (applyEntitePosition) == "undefined" || applyEntitePosition !== false) {
        applyEntitePosition = true;
    }

    var entite = recupererEntite(idEntite);
    entite.css({top: (y) + 'px', left: (x) + 'px', height: height + 'px'});

    if (applyEntitePosition) {
        var entitePosition = recupererEntitePosition(idEntite);
        entitePosition.css({height: height + 'px'});
    }
    redimensionnerAutoEntite(idEntite);
}

function recupererActionActive() {
    return jQuery('#actions input#action-entite').val();
}

function initialiserAction(idEntite) {
    if (estAValider()) {
        validerActionMessage();
        return false;
    }

    if ($('#lasso-help').is(':visible')) {
        $('#lasso-help').hide();
    }

    debloquerAction();
    jQuery('#action-entite-id').val(idEntite);
    jQuery('#action').css('display', 'block');

    //action par défaut
    deplacerEntite(idEntite);

    return true;
}

function terminerAction() {
    debloquerAction();

    jQuery('#action-entite-id').val('');
    jQuery('#action-entite').val('');
    jQuery('#actions').css('display', 'none');

    terminerActionParametrage();
    terminerRotationComposition();
}

var bAction = false;
var aValider = false;

function estBloque() {
    return bAction;
}

function bloquerAction(action) {
    bAction = true;
    if (action != 'action-deplacer') {
        jQuery('#actions div.action').addClass('inactif');
        jQuery('#' + action).removeClass('inactif');
        jQuery('#' + action).addClass('actif');
    }
    jQuery('#actions input#action-entite').val(action);
}

function debloquerAction() {
    if (estAValider()) {
        validerActionMessage();
        return false;
    }

    bAction = false;
    var idEntite = recupererEntiteIdCourante();
    arreterGommerEntite(idEntite);
    arreterDeplacerEntite(idEntite);
    arreterRotation(idEntite);
    arreterTransformation(idEntite);
    jQuery('#actions div').removeClass('inactif actif');
    jQuery('#actions input#action-entite').val('');
    terminerActionParametrage();
    //supprimerEntiteClone();

    return true;
}

function estAValider() {
    return aValider;
}

function validerAction() {
    if (estAValider())
        return;

    aValider = true;
    jQuery('#actions div.action-validation-hook').removeClass('non-visible');
    centrerDivVerticalement('action');
    cloneEntite(recupererEntiteIdCourante(), 'clone');
}

function validerOkAction() {
    aValider = false;
    recupererEntite(recupererEntiteIdCourante()).attr('attr-envoyer-image', '1');
    jQuery('#actions div.action-validation-hook').addClass('non-visible');
    centrerDivVerticalement('action');
    debloquerAction();
}

function validerActionMessage() {
    toggleConfirmationModification();

    jQuery('#popin-confirmation-modification #popin-confirmation-modification-ko').click(function () {
        toggleConfirmationModification();
        jQuery(this).unbind("click");
        jQuery('#popin-confirmation-modification #popin-confirmation-modification-ok').unbind("click");
        jQuery('#popin-confirmation-modification #popin-confirmation-modification-fermer').unbind("click");
        debloquerGommage('refuse');
    });

    jQuery('#popin-confirmation-modification #popin-confirmation-modification-ok').click(function () {
        toggleConfirmationModification();
        jQuery(this).unbind("click");
        jQuery('#popin-confirmation-modification #popin-confirmation-modification-ko').unbind("click");
        jQuery('#popin-confirmation-modification #popin-confirmation-modification-fermer').unbind("click");
        debloquerGommage('accepte');
    });

    jQuery('#popin-confirmation-modification #popin-confirmation-modification-fermer').click(function () {
        toggleConfirmationModification();
        jQuery(this).unbind("click");
        jQuery('#popin-confirmation-modification #popin-confirmation-modification-ok').unbind("click");
        jQuery('#popin-confirmation-modification #popin-confirmation-modification-ko').unbind("click");
    });

}

function replierInterface() {
    jQuery('#entete').css('display', 'none');
    jQuery('#menu').css('display', 'none');
    jQuery('#bloc-ma-selection').css('width', '0px');
}

function deplierInterface() {
    jQuery('#entete').css('display', 'block');
    jQuery('#menu').css('display', 'block');
}

function lancerVideoAide(outils, mode) {
    jQuery('.mjmt-app-aide-video').each(function () {
        if (jQuery(this).attr('attr-video-outils') === outils)
            jQuery(this).addClass('current');
        else
            jQuery(this).removeClass('current');
    });

    if(mode == 'open' && premierDemarrage()){
        jQuery('#premier-demarrage').val('1');
    }

    toggleAide(mode);
}

function calculTailleBarreOutils() {
    var count = jQuery('#actions-liste a:visible').length;
    jQuery('#actions').css('width', (((count + 1) * 65) - 40) + 'px');
    centrerFenetreActions();
}

function initRotationComposition() {
    jQuery('#action-rotation-gauche').css('display', 'inline');
    jQuery('#action-rotation-droite').css('display', 'inline');
    calculTailleBarreOutils();
}

function cacherRotationComposition() {
    jQuery('#action-rotation-gauche').css('display', 'none');
    jQuery('#action-rotation-droite').css('display', 'none');
    calculTailleBarreOutils();
}

function terminerRotationComposition() {
    jQuery('#accroche-rotation-composition').remove();
}

function masquerRotationComposition() {
    jQuery('#accroche-rotation-composition').css('display', 'none');
    calculTailleBarreOutils();
}

function afficherRotationComposition() {
    jQuery('#accroche-rotation-composition').css('display', 'block');
    calculTailleBarreOutils();
}

function rotationComposition(idEntite, sens) {
    var url = jQuery('#entite-photo-' + idEntite).attr('attr-rotation-composition-href');

    jQuery.ajax({
        url: url,
        data: "sens=" + sens + "&composition-vue-id=" + jQuery('#entite-photo-' + idEntite).attr('attr-composition-vue-id'),
        type: 'POST',
        dataType: "json",
        success: function (entiteJson) {
            var idEntite = jQuery(entiteJson.maSelection).find('input.entite-id').val();
            ajouterEntiteContenu(entiteJson.contenu);
            ajouterEntiteMaSelection(entiteJson.maSelection);

            //Si pas d'attente de validation d'action on sélectionne la nouvelle entité sinon on la met en arriere plan
            if (!estAValider())
                clickEntiteMaSelection(idEntite);
            else
                jQuery('#contenu div#entite-photo-' + idEntite).addClass('entite-en-arriere');

            fermerSelection();
            debloquerFenetreSelectionEntite();
            ouvrirMaSelection();
            setTimeout(function () {
                fermerMaSelection();
            }, 3000);
        }
    });
    jQuery('#entite-photo-' + idEntite).remove();
    jQuery('#maSelection-entite-' + idEntite).remove();
}

function modifierActionEntite(idEntite) {
    var $divEntite = recupererEntite(idEntite);

    if (typeof $divEntite.attr('attr-lasso') != "undefined" && $divEntite.attr('attr-lasso') == "1") {
        $('#action-duplication').attr('attr-disabled', '1');
        $('#action-duplication').hide();

        $('#action-agrandie').attr('attr-disabled', '1');
        $('#action-agrandie').hide();

        $('#action-diminue').attr('attr-disabled', '1');
        $('#action-diminue').hide();
    } else {
        $('#action-duplication').removeAttr('attr-disabled');
        $('#action-duplication').show();

        $('#action-agrandie').removeAttr('attr-disabled');
        $('#action-agrandie').show();

        $('#action-diminue').removeAttr('attr-disabled');
        $('#action-diminue').show();
    }
}

var interval = null;
var tailleGomme = 27;
jQuery(document).ready(function () {

    // Initialisation des tipsy
    if (!isTouchDevice()) {
        jQuery('.action-tipsy').tipsy({
            gravity: 'w',
            opacity: 1
        });
    }

    //Initialiser le centrage vertical des actions
    centrerDivVerticalement('action');

    reglerHauteurMaSelection();

    // Le centrage des actions se fait lors des resizes
    jQuery(window).resize(function () {
        centrerDivVerticalement('action');
        centrerDiv('fenetre-validation');
        centrerDiv('fenetre-confirmation');
        centrerDiv('fenetre-info-tampon');
        reglerHauteurMaSelection();
    });

    // Listener les actions possibles sur une entite
    jQuery('body').on('click', '#actions #actions-liste a', function () {
        //debloquerAction();

        var idEntite = recupererEntiteIdCourante();
        var idAction = jQuery(this).attr('id');

        var isDisabled = false;
        if (typeof $('#' + idAction).attr('attr-disabled') != "undefined" && $('#' + idAction).attr('attr-disabled') == 1) {
            isDisabled = true;
        }

        switch (idAction) {
            case 'action-gomme':
                isErasing = false;
                gommerEntite(idEntite);
                break;

            case 'action-symetrie':
                symetrieEntite(idEntite);
                break;

            case 'action-visibilite':
                visibiliteEntite(idEntite);
                jQuery('#maSelection-entite-' + idEntite + ' div.entite-image').toggleClass('entite-image-invisible');
                jQuery('#maSelection-entite-' + idEntite + ' div.entite-infos').toggleClass('entite-infos-invisible');
                break;

            case 'action-suppression':
                suppressionEntite(idEntite, false);
                break;

            case 'action-duplication':
                if (!isDisabled) {
                    clicSelectionEntite(jQuery('#maSelection-entite-' + idEntite + ' div.entite-action div.entite-dupliquer a').attr('href'), null, null, null, idEntite);
                }
                break;
            case 'action-diminue':
                clearInterval(interval);
                break;

            case 'action-agrandie':
                clearInterval(interval);
                break;

            case 'action-rotation':
                rotationEntite(idEntite);
                break;

            case 'action-transformation':
                transformationEntite(idEntite);
                break;

            case 'action-avant':
                avantEntite(idEntite);
                break;

            case 'action-arriere':
                arriereEntite(idEntite);
                break;

            case 'action-annulation':
                afficherRotationComposition();
                annulerEntite(idEntite);
                break;

            case 'action-validation':
                afficherRotationComposition();
                validerEntite(idEntite);
                break;
        }

        return false;
    });

    jQuery('body').on('touchend', '#actions div', function () {
        var idAction = jQuery(this).attr('id');

        if (idAction == 'action-annulation' || idAction == 'action-validation') {
            jQuery(this).click();
        }
    });

    // Listener pour arréter l'agrandissement ou diminution automatique
    jQuery('body').on('mouseleave', '#actions a', function () {
        clearInterval(interval);
    }).mouseup(function () {
        clearInterval(interval);
    });

    // Listener pour arréter l'agrandissement ou diminution automatique - Tablette
    jQuery('body').on('touchend', '#actions a', function () {
        clearInterval(interval);
    });

    // Listener les actions possibles sur une entite
    jQuery('body').on('mousedown', '#actions a', function () {
        //debloquerAction();

        var idEntite = recupererEntiteIdCourante();
        var idAction = jQuery(this).attr('id');

        var isDisabled = false;
        if (typeof $('#' + idAction).attr('attr-disabled') != "undefined" && $('#' + idAction).attr('attr-disabled') == 1) {
            isDisabled = true;
        }

        switch (idAction) {
            case 'action-diminue':
                if (!isDisabled) {
                    diminuerEntite(idEntite);
                    interval = setInterval("diminuerEntite('" + idEntite + "')", 50);
                }
                break;

            case 'action-agrandie':
                if (!isDisabled) {
                    agrandirEntite(idEntite);
                    interval = setInterval("agrandirEntite('" + idEntite + "')", 50);
                }
                break;
        }
    });

    // Listener les actions possibles sur une entite - Tablette
    jQuery('body').on('touchstart', '#actions a', function () {
        //debloquerAction();

        var idEntite = recupererEntiteIdCourante();
        var idAction = jQuery(this).attr('id');

        var isDisabled = false;
        if (typeof $('#' + idAction).attr('attr-disabled') != "undefined" && $('#' + idAction).attr('attr-disabled') == 1) {
            isDisabled = true;
        }

        switch (idAction) {
            case 'action-diminue':
                if (!isDisabled) {
                    diminuerEntite(idEntite);
                    interval = setInterval("diminuerEntite('" + idEntite + "')", 50);
                }
                break;

            case 'action-agrandie':
                if (!isDisabled) {
                    agrandirEntite(idEntite);
                    interval = setInterval("agrandirEntite('" + idEntite + "')", 50);
                }
                break;
        }
    });

    // Listener les actions possibles sur une entite
    jQuery('body').on('keydown', '', function (e) {
        //debloquerAction();
        if (recupererActionActive() == 'action-deplacer') {
            var idEntite = recupererEntiteIdCourante();

            switch (e.keyCode) {
                case 40: // bas
                    deplacerXYEntite(idEntite, 0, 1);
                    break;

                case 38: // haut
                    deplacerXYEntite(idEntite, 0, -1);
                    break;

                case 37: // gauche
                    deplacerXYEntite(idEntite, -1, 0);
                    break;

                case 39: // haut
                    deplacerXYEntite(idEntite, 1, 0);
                    break;

                case 46: // Suppression
                    suppressionEntite(idEntite, false);
                    break;

                case 27:
                    terminerAction();
                    terminerContenu();
                    terminerMaSelection();
                    break;
            }
        }
    });

    jQuery('body').on('click', '.afficher-video-aide', function () {
        lancerVideoAide(jQuery(this).attr('attr-video-action'), "open");
        return false;
    });

    jQuery('body').on('click', '#action-rotation-gauche', function () {
        var idEntite = recupererEntiteIdCourante();
        rotationComposition(idEntite, 'gauche');
    });

    jQuery('body').on('click', '#action-rotation-droite', function () {
        var idEntite = recupererEntiteIdCourante();
        rotationComposition(idEntite, 'droite');
    });

    jQuery('#gomme-confirmation-modification-ko').click(function () {
        if (isErasing) {
            debloquerGommage('refuse');
        } else {
            var idEntite = recupererEntiteIdCourante();
            gommerEntite(idEntite);
        }
    });

    jQuery('#gomme-confirmation-modification-ok').click(function () {
        debloquerGommage('accepte');
    });

});

function toggleConfirmation() {
    if (jQuery('#popin-confirmation').css('display') === 'none')
        jQuery('#popin-confirmation').fadeIn();
    else
        jQuery('#popin-confirmation').fadeOut();

    initialiserFenetreSelection('#popin-confirmation-contenu');
}

function toggleConfirmationModification() {
    if (jQuery('#popin-confirmation-modification').css('display') === 'none')
        jQuery('#popin-confirmation-modification').fadeIn();
    else
        jQuery('#popin-confirmation-modification').fadeOut();

    initialiserFenetreSelection('#popin-confirmation-modification-contenu');
}

function toggleQuitter() {
    if (jQuery('#popin-quitter').css('display') === 'none')
        jQuery('#popin-quitter').fadeIn();
    else
        jQuery('#popin-quitter').fadeOut();

    initialiserFenetreSelection('#popin-quitter-contenu');
}

function toggleFinaliser() {
    if (jQuery('#popin-finaliser').css('display') === 'none')
        jQuery('#popin-finaliser').fadeIn();
    else
        jQuery('#popin-finaliser').fadeOut();

    initialiserFenetreSelection('#popin-finaliser-contenu');
}

function debloquerGommage(action) {
    bAction = false;
    aValider = false;
    var idEntite = recupererEntiteIdCourante();
    arreterGommerEntite(idEntite);
    arreterDeplacerEntite(idEntite);
    jQuery('#actions div').removeClass('inactif actif');
    jQuery('#actions input#action-entite').val('');
    terminerActionParametrage();

    if (action === 'accepte')
        validerEntite(idEntite);
    else if (action === 'refuse')
        annulerEntite(idEntite);
}

function reglerHauteurMaSelection() {
    var heightWindow = jQuery(window).height();
    var heightHeader = jQuery('#entete').height();
    var newHeight = heightWindow - heightHeader - 18;

    jQuery('#bloc-ma-selection').css('height', newHeight + 'px');
}