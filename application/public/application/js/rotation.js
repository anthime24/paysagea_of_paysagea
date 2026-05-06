var degres = 0;

function initialiserRotation() {
    if (!debloquerAction())
        return false;

    terminerAction();
    terminerProportion();
    masquerEntites();
    terminerContenu();
    replierInterface();

    jQuery('#entete-outil').html('');
    jQuery('#entete-outil').append('<div id="accroche-rotation-message">' +
        '<div class="menu-bloc-logo"><img src="/application/images/logo.png"></div>' +
        '<div class="menu-bloc-content">' +
        '<div class="container">' +
        '<p>' +
        translateJs('rotation.header') +
        '</p>' +
        '<div>' +
        '</div>' +
        '</div>');
    jQuery('#entete-outil').show();
    //centrerDivHorizontal('accroche-rotation-message');

    jQuery('body').append('<div id="accroche-rotation-outils"> ' +
        '<div class="tool-button"><div id="btn-rotation"></div><label>' + translateJs('menuoutil.rotation') + '</label></div>' +
'</div>');

    jQuery('body').append('<div id="accroche-rotation-validation"> ' +
        '<div class="tool-button"><div id="acp-annulation"></div><label>' + translateJs('menuoutil.annuler') + '</label></div>' +
        '<div class="tool-button"><div id="acp-validation" original-title="Validez pour revenir à votre création"></div><label>' + translateJs('menuoutil.confirmer') + '</label></div>' +
        '</div>');


    jQuery('#acp-validation').tipsy({gravity: 'w'});

    var imgToCanvas = jQuery('#contenu-fond-image img')[0];
    var saveImgSrc = jQuery(imgToCanvas).attr('src');
    var saveImgHeight = jQuery(imgToCanvas).height();
    var saveImgWidth = jQuery(imgToCanvas).width();

    jQuery('#acp-annulation').click(function () {
        retablirImage(imgToCanvas, saveImgSrc, saveImgHeight, saveImgWidth);
        terminerRotation();
    });

    jQuery('#acp-validation').click(function () {
        sauvegarderRotation();
    });

    //Action
    jQuery('#btn-rotation').click(function () {
        if ((jQuery("#canvasRotation").length == 0)) {
            jQuery('#contenu-fond-image').append('<canvas id="canvasRotation" style="display: none;"></canvas>');
        }
        var canvas = jQuery('#canvasRotation')[0];
        var contextCanvas = canvas.getContext('2d');
        var imgToCanvas = jQuery('#contenu-fond-image img')[0];
        jQuery(imgToCanvas).css('height', 'auto');
        jQuery(imgToCanvas).css('width', 'auto');

        degres = parseInt(degres) + 90;
        degres = degres % 360;

        if (imgToCanvas) {
            canvas.width = imgToCanvas.width;
            canvas.height = imgToCanvas.height;
            contextCanvas.drawImage(imgToCanvas, 0, 0);
            // jQuery('#contenu-fond-image img').hide();
        }

        drawRotated(contextCanvas, canvas, imgToCanvas);
    });
}


function retablirImage(image, src, height, width) {
    jQuery(image).attr('src', src);
    jQuery(image).attr('width', width);
    jQuery(image).attr('height', height);

    jQuery('#contenu').css('width', width);
    jQuery('#contenu').css('height', height);
}


function drawRotated(ctx, canvas, image) {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.save();
    canvas.width = image.height;
    canvas.height = image.width;

    jQuery('#contenu').css('height', image.width);
    jQuery('#contenu').css('width', image.height);

    ctx.translate(canvas.width / 2, canvas.height / 2);
    ctx.rotate(90 * Math.PI / 180);
    ctx.drawImage(image, -image.width / 2, -image.height / 2);
    image.src = canvas.toDataURL();
    ctx.restore();
}

jQuery(window).resize(function () {
    centrerDivHorizontal('accroche-rotation-message');
});


function sauvegarderRotation() {
    if (jQuery('#canvasRotation')[0] && degres != 0) {
        toggleEnregistrement();
        var dataURL = jQuery('#canvasRotation')[0].toDataURL("image/jpeg", 1.0);
        degres = 0;

        jQuery.ajax({
            url: jQuery('#url-tampon').val(),
            dataType: 'html',
            type: "POST",
            data: {
                imgDataUrl: dataURL
            },
            success: function (data) {
                toggleEnregistrement();

                jQuery('#accroche-rotation-validation').remove();
                jQuery('#accroche-rotation-outils').remove();
                jQuery('#accroche-rotation-message').remove();
                jQuery('#canvasRotation').remove();

                reinitialiserProportion();
                centrerImageFond();
            },
            error: function (data) {
                toggleEnregistrement();
                terminerRotation();
            }
        });
    } else {
        terminerRotation();
    }
}

function terminerRotation() {
    jQuery('.tipsy').remove();

    jQuery('#accroche-rotation-validation').remove();
    jQuery('#accroche-rotation-outils').remove();
    jQuery('#accroche-rotation-message').remove();
    jQuery('#canvasRotation').remove();

    jQuery('#entete-outil').html('');
    jQuery('#entete-outil').hide();

    deplierInterface();
    afficherEntites();
    centrerImageFond();
}