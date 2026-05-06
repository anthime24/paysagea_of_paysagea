var etatOutils = "pointeur";
var bodyTamponOffsetLeft = null;
var contenuImageTamponOffsetTop = null;
var tamponWindowsWidth = null;
var tamponResizeTimeout = null;


var tamponEventHandlers = {
    resizeHandler: null,
    contenuMouseMoveHandler: null,
    contenuMouseUpHandler1: null,
    contenuMouseUpHandler2: null,
    bodyMousedownMouseDownCanvasTamponHandler: null,
    canvasTamponMouseDownMouseDownCanvasTamponHandler: null,
    canvasTamponMouseUpCloneCanvasHandler: null,
    contenuMouseEnterMouseEnterCanvasTamponHandler: null,
    documentMousemoveMouseMoveTamponHandler: null
};


function initialiserTampon(bPremierDemarrage) {

    if(typeof bPremierDemarrage == 'undefined') {
        bPremierDemarrage = false;
    }

    tamponWindowsWidth = $(window).width();

    tamponEventHandlers = {
        contenuMouseMoveHandler: null,
        contenuMouseUpHandler1: null,
        contenuMouseUpHandler2: null,
        bodyMousedownMouseDownCanvasTamponHandler: null,
        canvasTamponMouseDownMouseDownCanvasTamponHandler: null,
        canvasTamponMouseUpCloneCanvasHandler: null,
        contenuMouseEnterMouseEnterCanvasTamponHandler: null,
        documentMousemoveMouseMoveTamponHandler: null
    };

    if (isTouchDevice())
        return false;

    if (!debloquerAction())
        return false;

    terminerAction();
    terminerProportion();
    masquerEntites();
    terminerContenu();
    replierInterface();

    initialiserTamponParametrage('taille-tampon');

    if (premierDemarrage()) {
        if(jQuery('html').attr('lang') == 'fr') {
            lancerVideoAide('tampon', 'open');
        }
    }

    //le 168px représente un fix pour la margin de monjardinmaterrasse
    bodyTamponOffsetLeft = jQuery('#contenu').offset().left;
    contenuImageTamponOffsetTop = jQuery('#contenu').offset().top + 60;
    jQuery('body').css('padding-top', contenuImageTamponOffsetTop);

    jQuery('#entete-outil').html('');

    //modification spécifique pour l'outil tampon
    jQuery('#entete-outil').css('position', 'absolute');
    jQuery('#entete-outil').css('top', '0px');
    jQuery('#entete-outil').css('left', '0px');
    jQuery('#entete-outil').css('width', '100%');

    var contenuTexte = translateJs("tampon.header");
    if(bPremierDemarrage === true) {
        contenuTexte = contenuTexte + translateJs("tampon.header2");
    }

    jQuery('#entete-outil').append('<div id="accroche-tampon-message">' +
        '<div class="menu-bloc-logo"><img src="/application/images/logo.png"></div>' +
    '<div class="menu-bloc-content">' +
    '<div class="container">' +
     '<p>' + contenuTexte + '</p>' +
    '</div>' +
    '</div>');
    jQuery('#entete-outil').show();
    //centrerDivHorizontal('accroche-tampon-message');

    jQuery('body').append('<div id="accroche-tampon-outils"> ' +
        '<div class="tool-button"><div id="btn-pointeur"></div><label>' + translateJs('menuoutil.pointeur') + '</label></div>' +
        '<div class="tool-button"><div id="btn-tampon"></div><label>' + translateJs('menuoutil.gomme') + '</label></div>' +
        '</div>');

    jQuery('body').append('<div id="accroche-tampon-validation"> ' +
        '<div class="tool-button"><div id="acp-annulation"></div><label>' + translateJs('menuoutil.annuler') + '</label></div>' +
        '<div class="tool-button"><div id="acp-validation"></div><label>' + translateJs('menuoutil.confirmer') + '</label></div>' +
        '</div>');

    if (contenuImageTamponOffsetTop > 0) {
        jQuery('#accroche-tampon-outils').css('top', Math.round(jQuery('#accroche-tampon-outils').offset().top + contenuImageTamponOffsetTop));
        jQuery('#accroche-tampon-validation').css('top', Math.round(jQuery('#accroche-tampon-validation').offset().top + contenuImageTamponOffsetTop) + 20);
    }

    jQuery('#contenu-fond-image').append('<div id="curseur-tampon"></div>');
    //jQuery('#curseur-tampon').off();

    if ((jQuery("#canvasTampon").length == 0)) {
        etatOutils = "pointeur";
        jQuery('#contenu-fond-image').append('<div id="canvasTamponContainer" style="height: ' + (jQuery('#contenu-fond-image img').height() + 30) + '"><canvas id="canvasTampon" width="' + jQuery('#contenu-fond-image img').width() + '" height="' + jQuery('#contenu-fond-image img').height() + '"></canvas></div>');
    }
    jQuery('#contenu-fond-image').append('<canvas id="canvasTamponClone" width="' + jQuery('#canvasTampon').width() + '" height="' + jQuery('#canvasTampon').height() + '" style="display:none;"></canvas>');
    jQuery('#contenu-fond-image').append('<canvas id="canvasTamponCloneBase" width="' + jQuery('#canvasTampon').width() + '" height="' + jQuery('#canvasTampon').height() + '" style="display:none;"></canvas>');
    jQuery('#contenu-fond-image').append('<div id="temoin-pointeur" style="width:' + recupererTailleTampon() + 'px;height:' + recupererTailleTampon() + 'px;"></div>');

    //Canvas
    var canvas = jQuery('#canvasTampon')[0];
    var contextCanvas = canvas.getContext('2d');
    if ((jQuery("#contenu-fond-image img").length > 0)) {
        var imgToCanvas = jQuery('#contenu-fond-image img')[0];
        contextCanvas.drawImage(imgToCanvas, 0, 0);
        jQuery('#contenu-fond-image img').hide();
    }
    clonerCanvas('canvasTampon', 'canvasTamponClone');
    clonerCanvas('canvasTampon', 'canvasTamponCloneBase');

//    centrerDivHorizontal('accroche-tampon-outils');
//    jQuery('#accroche-tampon-outils').css('left', (jQuery('#accroche-tampon-outils').position().left - 303) + 'px');
//    centrerDivHorizontal('accroche-tampon-validation');
//    jQuery('#accroche-tampon-validation').css('left', (jQuery('#accroche-tampon-validation').position().left + 303) + 'px');
    jQuery('#btn-pointeur').css('border', '1px solid #4F4F4F');
    jQuery('#btn-pointeur').css('border-radius', '12px');

    var isBaseDuplicationSet = false;
    var baseDuplication;

    var pointDepartTampon;

    jQuery('#acp-validation').click(function () {
        etatOutils = "pointeur";
        sauvegarderTampon();
    });

    jQuery('#acp-annulation').click(function () {
        var imgData = jQuery('#canvasTamponCloneBase')[0].getContext('2d').getImageData(0, 0, jQuery('#canvasTamponCloneBase').width(), jQuery('#canvasTamponCloneBase').height());
        contextCanvas.putImageData(imgData, 0, 0);

        etatOutils = "pointeur";
        terminerTampon();

        if (premierDemarrageSansProportion()) {
            ouvrirSelection();
            finirPremierDemarrageSansProportion();
        }
    });

    // Initialisation des tipsy   
    if (!isTouchDevice()) {
        jQuery('.acp-tipsy').tipsy({
            gravity: 's',
            opacity: 1
        });
    }

    jQuery('#btn-pointeur').click(function () {
        jQuery('#btn-pointeur').css('border', '1px solid #4F4F4F');
        jQuery('#btn-pointeur').css('border-radius', '12px');
        jQuery('#btn-tampon').css('border', 'none');
        etatOutils = "pointeur";
    });

    jQuery('#btn-tampon').click(function () {
        jQuery('#btn-tampon').css('border', '1px solid #4F4F4F');
        jQuery('#btn-tampon').css('border-radius', '12px');
        jQuery('#btn-pointeur').css('border', 'none');
        etatOutils = "tampon";
    });


    //Actions
    /*
    jQuery('body').on('mousedown', '#curseur-tampon', function(e) {
        jQuery('#canvasTampon').trigger('mousedown', [e]);
    });

    jQuery('body').on('mouseup', '#curseur-tampon', function(e) {
        jQuery('#canvasTampon').trigger('mouseup', [e]);
    });
    */

    function mouseDownCanvasTampon(e) {
        e.preventDefault();
        e.stopPropagation();

        if (etatOutils == "tampon") {
            if (isBaseDuplicationSet) {
                var coordCanvas = recupererCoordCanvas(e);
                pointDepartTampon = {
                    x: coordCanvas.x,
                    y: coordCanvas.y
                };

                var coordCanvas = recupererCoordCanvas(e);
                var coordsTampon = {
                    x: coordCanvas.x,
                    y: coordCanvas.y
                };
                var donneesTampon = recupererDonneesCanvas(baseDuplication, coordsTampon, pointDepartTampon);
                if (donneesTampon != null) {
                    var imgData = donneesTampon.imgData;
                    var curseurX = donneesTampon.curseurX;
                    var curseurY = donneesTampon.curseurY;
                    contextCanvas.putImageData(imgData, curseurX, curseurY);
                }

                if (tamponEventHandlers.contenuMouseMoveHandler === null) {
                    tamponEventHandlers.contenuMouseMoveHandler = function (e) {
                        var coordCanvas = recupererCoordCanvas(e);
                        var coordsTampon = {
                            x: coordCanvas.x,
                            y: coordCanvas.y
                        };
                        var donneesTampon = recupererDonneesCanvas(baseDuplication, coordsTampon, pointDepartTampon);
                        if (donneesTampon != null) {
                            var imgData = donneesTampon.imgData;
                            var curseurX = donneesTampon.curseurX;
                            var curseurY = donneesTampon.curseurY;
                            contextCanvas.putImageData(imgData, curseurX, curseurY);
                        }
                    };

                    jQuery('#contenu').bind('mousemove', tamponEventHandlers.contenuMouseMoveHandler);
                }


                if (tamponEventHandlers.contenuMouseUpHandler1 === null) {
                    tamponEventHandlers.contenuMouseUpHandler1 = function (e) {
                        e.preventDefault();
                        e.stopPropagation();

                        jQuery('#contenu').unbind('mousemove');
                        tamponEventHandlers.contenuMouseMoveHandler = null;
                        ajusterTemoinPointeur(baseDuplication.x, baseDuplication.y);
                    }

                    jQuery('#contenu').bind('mouseup', tamponEventHandlers.contenuMouseUpHandler1);
                }
            } else {
                jQuery('#popin-info-tampon').fadeIn();
                centrerDiv('fenetre-info-tampon');

                jQuery('#popin-info-tampon #confirmation-oui').click(function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    jQuery('#popin-info-tampon').fadeOut();
                    jQuery(this).unbind("click");
                });
            }
        } else {
            if (tamponEventHandlers.contenuMouseUpHandler2 === null) {
                tamponEventHandlers.contenuMouseUpHandler2 = function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    if (etatOutils == "pointeur") {
                        var coordCanvas = recupererCoordCanvas(e);
                        baseDuplication = {
                            x: coordCanvas.x,
                            y: coordCanvas.y
                        };
                        isBaseDuplicationSet = true;

                        jQuery('#temoin-pointeur').css('display', 'block');
                        ajusterTemoinPointeur(baseDuplication.x, baseDuplication.y);
                    }
                };

                jQuery('#contenu').bind('mouseup', tamponEventHandlers.contenuMouseUpHandler2);
            }

        }
    }

    if (tamponEventHandlers.bodyMousedownMouseDownCanvasTamponHandler === null) {
        tamponEventHandlers.bodyMousedownMouseDownCanvasTamponHandler = function (e) {
            if ($('#popin-aide').is(':visible') == false && $('#canvasTampon').length > 0) {

                var coordCanvas = recupererCoordCanvas(e);
                var canvasWidth = $('#canvasTampon').width();
                var canvasHeight = $('#canvasTampon').height();
                var tailletampon = recupererTailleTampon() / 2;

                var verifX = false;
                if (coordCanvas.x - tailletampon > -tailletampon && coordCanvas.x - tailletampon < canvasWidth) {
                    verifX = true;
                }

                var verifY = false;
                if (coordCanvas.y - tailletampon > -tailletampon && coordCanvas.y - tailletampon < canvasHeight) {
                    verifY = true;
                }


                var coordBarreOutil = $('#accroche-tampon-outils').offset();

                //fix le tampon se met en route quand on clique de nouveau sur l'outil pointeur
                var verifBarreOutilX = true;
                if (coordCanvas.x + $('#contenu').offset().left >= coordBarreOutil.left && coordCanvas.x + $('#contenu').offset().left <= coordBarreOutil.left + $('#accroche-tampon-outils').width()) {
                    if (etatOutils == "tampon") {
                        verifBarreOutilX = false;
                    }
                }

                var verifBarreOutilY = true;
                if (coordCanvas.y + $('#contenu').offset().top >= coordBarreOutil.top && coordCanvas.y + $('#contenu').offset().top <= coordBarreOutil.top + $('#accroche-tampon-outils').height()) {
                    if (etatOutils == "tampon") {
                        verifBarreOutilY = false;
                    }
                }

                if (verifBarreOutilX === false && verifBarreOutilY === false) {
                    return;
                }

                if (verifX && verifY) {
                    mouseDownCanvasTampon(e);
                }
            }
        }
        jQuery('body').on('mousedown', tamponEventHandlers.bodyMousedownMouseDownCanvasTamponHandler);
    }

    if (tamponEventHandlers.canvasTamponMouseDownMouseDownCanvasTamponHandler === null) {
        tamponEventHandlers.canvasTamponMouseDownMouseDownCanvasTamponHandler = function (e) {
            mouseDownCanvasTampon(e);
        };

        jQuery('#canvasTampon').bind('mousedown', tamponEventHandlers.canvasTamponMouseDownMouseDownCanvasTamponHandler);
    }

    if (tamponEventHandlers.canvasTamponMouseUpCloneCanvasHandler === null) {
        tamponEventHandlers.canvasTamponMouseUpCloneCanvasHandler = function (e) {
            clonerCanvas('canvasTampon', 'canvasTamponClone');
        }

        jQuery('#canvasTampon').bind('mouseup', tamponEventHandlers.canvasTamponMouseUpCloneCanvasHandler);
    }

    function mouseEnterCanvasTampon(e, tailleTampon) {
        document.getElementById('curseur-tampon').style.display = 'block';
        document.getElementById('curseur-tampon').style.left = (e.pageX - (tailleTampon / 2) - document.getElementById('contenu').offsetLeft) + 'px';
        document.getElementById('curseur-tampon').style.top = (e.pageY - (tailleTampon / 2) - document.getElementById('contenu').offsetTop) + 'px';
        document.getElementById('curseur-tampon').style.width = tailleTampon + 'px';
        document.getElementById('curseur-tampon').style.height = tailleTampon + 'px';
        document.getElementsByTagName("body")[0].style.cursor = 'none';
    }

    if (tamponEventHandlers.contenuMouseEnterMouseEnterCanvasTamponHandler === null) {
        tamponEventHandlers.contenuMouseEnterMouseEnterCanvasTamponHandler = function (e) {
            if ($('#popin-aide').is(':visible') == false && $('#canvasTampon').length > 0) {
                var coordCanvas = recupererCoordCanvas(e);
                var canvasWidth = $('#canvasTampon').width();
                var canvasHeight = $('#canvasTampon').height();
                var tailletampon = recupererTailleTampon() / 2;

                /*var verifX = false;
                if((coordCanvas.x - tailletampon > -tailletampon) && (coordCanvas.x - tailletampon < canvasWidth)) {
                    verifX = true;
                }

                var verifY = false;
                if((coordCanvas.y - tailletampon > -tailletampon) && (coordCanvas.y - tailletampon < canvasHeight)) {
                    verifY = true;
                }*/

                mouseEnterCanvasTampon(e, tailleTampon);
            }
        };

        jQuery('#contenu').on('mouseenter', tamponEventHandlers.contenuMouseEnterMouseEnterCanvasTamponHandler);
    }

    function mouseMoveTampon(e) {

        var pageX = e.pageX;
        var pageY = e.pageY;
        var contenuMarginTop = $('#contenu').offset().top;
        var contenuHeight = $('#contenu').height();


        document.getElementById('curseur-tampon').style.left = (e.pageX - (tailleTampon / 2) - document.getElementById('contenu').offsetLeft) + 'px';
        document.getElementById('curseur-tampon').style.top = (e.pageY - (tailleTampon / 2) - document.getElementById('contenu').offsetTop) + 'px';
        var cursor_top = document.getElementById('curseur-tampon').offsetTop;
        var cursor_left = document.getElementById('curseur-tampon').offsetLeft;

        if (cursor_top < (-1 * tailleTampon)
            || cursor_top > document.getElementById('contenu').offsetHeight
            || cursor_left > document.getElementById('contenu').offsetWidth
            || cursor_left < (-1 * tailleTampon)) {
            document.getElementById('curseur-tampon').style.display = 'none';
            document.getElementsByTagName("body")[0].style.cursor = 'auto';
        }
    }

    if (tamponEventHandlers.documentMousemoveMouseMoveTamponHandler === null) {
        tamponEventHandlers.documentMousemoveMouseMoveTamponHandler = function (e) {
            mouseMoveTampon(e);
        }

        jQuery(document).bind('mousemove', tamponEventHandlers.documentMousemoveMouseMoveTamponHandler);
    }
}

//taille tampon par défaut
var tailleTampon = 13;

function sauvegarderTampon() {
    toggleEnregistrement();

    var dataURL = jQuery('#canvasTampon')[0].toDataURL("image/jpeg", 1.0);
    jQuery('#contenu-fond-image img').attr('src', dataURL);

    jQuery.ajax({
        url: jQuery('#url-tampon').val(),
        dataType: 'html',
        type: "POST",
        data: {
            imgDataUrl: dataURL
        },
        success: function (data) {
            toggleEnregistrement();

            terminerTampon();

            if (premierDemarrageSansProportion()) {
                ouvrirSelection();
                finirPremierDemarrageSansProportion();
            }
        },
        error: function (data) {
            toggleEnregistrement();

            terminerTampon();

            if (premierDemarrageSansProportion()) {
                ouvrirSelection();
                finirPremierDemarrageSansProportion();
            }
        }
    });
}

function terminerTampon() {
    jQuery('#accroche-tampon-validation').remove();
    jQuery('#accroche-tampon-outils').remove();
    jQuery('#canvasTamponClone').remove();
    jQuery('#canvasTamponCloneBase').remove();
    jQuery('#temoin-pointeur').remove();
    jQuery('#accroche-tampon-message').remove();

    //modification spécifique à l'outil tampon
    jQuery('#entete-outil').css('position', '');
    jQuery('#entete-outil').css('top', '');
    jQuery('#entete-outil').css('left', '');
    jQuery('#entete-outil').css('width', '');

    jQuery('#entete-outil').html('');
    jQuery('#entete-outil').hide();

    if (!isTouchDevice())
        jQuery('.tipsy').remove();

    jQuery('#curseur-tampon').remove();

    jQuery.each(tamponEventHandlers, function (key, value) {
        if (value !== null) {
            if (key == 'contenuMouseMoveHandler') {
                jQuery('#contenu').off('mousemove', value);
            } else if (key == 'contenuMouseUpHandler1') {
                jQuery('#contenu').off('mouseup', value);
            } else if (key == 'contenuMouseUpHandler2') {
                jQuery('#contenu').off('mouseup', value);
            } else if (key == 'bodyMousedownMouseDownCanvasTamponHandler') {
                jQuery('body').off('mousedown', value);
            } else if (key == 'canvasTamponMouseDownMouseDownCanvasTamponHandler') {
                jQuery('#canvasTampon').off('mousedown', value);
            } else if (key == 'canvasTamponMouseUpCloneCanvasHandler') {
                jQuery('#canvasTampon').off('mouseup', value);
            } else if (key == 'contenuMouseEnterMouseEnterCanvasTamponHandler') {
                jQuery('#contenu').off('mouseenter', value);
            } else if (key == 'documentMousemoveMouseMoveTamponHandler') {
                jQuery(document).off('mousemove', value);
            } else if (key == 'resizeHandler') {
                jQuery(window).off('resize', value);
            }
        }
    });

    if (tamponResizeTimeout !== null) {
        clearTimeout(tamponResizeTimeout);
        tamponResizeTimeout = null;
    }


    jQuery('#canvasTampon').remove();
    jQuery('#contenu-fond-image img').show();

    terminerTamponParametrage();
    terminerProportion();
    deplierInterface();
    afficherEntites();

    if (bodyTamponOffsetLeft !== null) {
        jQuery('body').css('padding-top', '');
        jQuery('body').css('padding-left', '');
        jQuery('body').css('position', 'relative');
        jQuery('#contenu').css('left', '');

        jQuery('#contenu').css('margin-left', bodyTamponOffsetLeft);
    }

    if (premierDemarrage()) {
        ouvrirSelection();
        lancerVideoAide('tampon', 'close');
    }
}

/**
 *
 * @param event : évènement
 * @returns retourne les coordonées de l'évènement (click ou autre)
 */
function recupererCoordCanvas(event) {
    var ox = document.getElementById('contenu').offsetLeft - window.pageXOffset;
    var oy = document.getElementById('contenu').offsetTop - window.pageYOffset;

    return {x: event.clientX - ox, y: event.clientY - oy};
}

/**
 *
 * @param baseDuplication Coordonnées de la base duplication
 * @param coordsTampon Coordonnées (position) du tampon
 * @param pointDepartTampon Coordonées du premier click du tampon
 * @returns retroune un imageData & les coordonées à lui appliquer pour le putimageData
 */
function recupererDonneesCanvas(baseDuplication, coordsTampon, pointDepartTampon) {
    var baseX = baseDuplication.x + (coordsTampon.x - pointDepartTampon.x);
    var baseY = baseDuplication.y + (coordsTampon.y - pointDepartTampon.y);

    var selectionX = recupererTailleTampon() / 2;
    var selectionY = recupererTailleTampon() / 2;

    var x;
    var y;
    var curseurX;
    var curseurY;

    if (baseX > document.getElementById('canvasTampon').offsetWidth) {
        if (baseX - selectionX < document.getElementById('canvasTampon').offsetWidth) {
            selectionX = selectionX - (baseX - document.getElementById('canvasTampon').offsetWidth);
            x = document.getElementById('canvasTampon').offsetWidth - selectionX;
            curseurX = coordsTampon.x - recupererTailleTampon() / 2;
        } else {
            return null;
        }
    } else if (baseX <= 0) {
        if (selectionX + baseX > 0) {
            selectionX = selectionX + baseX;
            x = 0;
            curseurX = coordsTampon.x - baseX;
        } else {
            return null;
        }
    } else if (selectionX + baseX > document.getElementById('canvasTampon').offsetWidth) {
        selectionX = document.getElementById('canvasTampon').offsetWidth - baseX;
        x = baseX - selectionX / 2;
        curseurX = coordsTampon.x - selectionX / 2;
    } else if (baseX - selectionX <= 0) {
        selectionX = selectionX + baseX;
        x = 0;
        curseurX = coordsTampon.x;
    } else {
        selectionX = recupererTailleTampon();
        x = baseX - recupererTailleTampon() / 2;
        curseurX = coordsTampon.x - recupererTailleTampon() / 2;
    }

    if (baseY > document.getElementById('canvasTampon').offsetHeight) {
        if (baseY - selectionY < document.getElementById('canvasTampon').offsetHeight) {
            selectionY = selectionY - (baseY - document.getElementById('canvasTampon').offsetHeight);
            y = document.getElementById('canvasTampon').offsetHeight - selectionY;
            curseurY = coordsTampon.y - recupererTailleTampon() / 2;
        } else {
            return null;
        }
    } else if (baseY <= 0) {
        if (selectionY + baseY > 0) {
            selectionY = selectionY + baseY;
            y = 0;
            curseurY = coordsTampon.y - baseY;
        } else {
            return null;
        }
    } else if (selectionY + baseY > document.getElementById('canvasTampon').offsetHeight) {
        selectionY = document.getElementById('canvasTampon').offsetHeight - baseY;
        y = baseY - selectionY / 2;
        curseurY = coordsTampon.y - selectionY / 2;
    } else if (baseY - selectionY <= 0) {
        selectionY = selectionY + baseY;
        y = 0;
        curseurY = coordsTampon.y - baseY;
    } else {
        selectionY = recupererTailleTampon();
        y = baseY - recupererTailleTampon() / 2;
        curseurY = coordsTampon.y - recupererTailleTampon() / 2;
    }

    ajusterTemoinPointeur(baseX, baseY);
    var imgData = document.getElementById('canvasTamponClone').getContext('2d').getImageData(x, y, selectionX, selectionY);

    return {
        imgData: imgData,
        curseurX: curseurX,
        curseurY: curseurY
    };
}

function ajusterTemoinPointeur(x, y) {
    document.getElementById('temoin-pointeur').style.left = (x - recupererTailleTampon() / 2) + 'px';
    document.getElementById('temoin-pointeur').style.top = (y - recupererTailleTampon() / 2) + 'px';
}

function clonerCanvas(canvasBase, canvasClone) {
    var imgData = jQuery('#' + canvasBase)[0].getContext('2d').getImageData(0, 0, jQuery('#' + canvasBase).width(), jQuery('#' + canvasBase).height());
    jQuery('#' + canvasClone)[0].getContext('2d').putImageData(imgData, 0, 0);
}
