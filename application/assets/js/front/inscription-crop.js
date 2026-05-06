var inscriptionCrop = {
    angle: 0,
    jcrop_enabled: false,
    jcrop_api: null,
    coordonnees: null,
    width: null,
    height: null,
    inited: false
};

jQuery(document).ready(function () {
    if (jQuery('#page-inscription-crop').length !== 0) {
        var $container = jQuery('#mjmt_frontbundle_creation_inscription_3_crop_image_contenaire');
        var $img = $container.find('img');

        if($img.width() > 0) {
            initWidget($container, $img);
        } else {
            $img.on('load', function(){
                if(inscriptionCrop.inited === false) {
                    console.log('inited on load');
                    initWidget($container, $img);
                    inscriptionCrop.inited = true;
                }
            })
        }
    }
});

function initWidget($container, $img) {
    $container.width($img.width());
    jQuery('#inscription_crop_originalHeight').val(jQuery('.jcrop-holder').height());
    jQuery('#inscription_crop_originalWidth').val(jQuery('.jcrop-holder').width());


    var canvas = document.getElementById("canvas");
    var ctx = canvas.getContext("2d");
    var image = document.getElementById("crop-image-target");

    inscriptionCrop.width = image.width;
    inscriptionCrop.height = image.height;
    canvas.width = image.width;
    canvas.height = image.height;
    ctx.drawImage(image, (canvas.width - image.width), (canvas.height - image.height));

    jQuery('#btn-rotation').on('click', function (e) {
        e.preventDefault();
        inscriptionCrop.angle += 90;
        inscriptionCrop.angle = inscriptionCrop.angle % 360;
        jQuery('#inscription_crop_rotation').val(inscriptionCrop.angle);

        drawRotated(ctx, canvas, image);
        destroyJcrop();
    });

    jQuery('#btn-crop').on('click', function(e){
        if(inscriptionCrop.jcrop_enabled === false) {
            setAreaSelect(false);
        } else {
            destroyJcrop();
        }
    })

    jQuery('#btn-cancel').on('click', function(e){
        e.preventDefault();
        e.stopPropagation();
        cancel();
    })
}

function drawRotated(ctx, canvas, image, nbrRotationNeccessaire) {
    var angleRotation = 90;
    if(typeof nbrRotationNeccessaire == "undefined") {
        var nbrRotationNeccessaire = 1;
    } else {
        angleRotation = angleRotation * nbrRotationNeccessaire;
    }

    jQuery(image).hide();

    if (inscriptionCrop.angle == 90 || inscriptionCrop.angle == 270)
        jQuery(image).css({width: inscriptionCrop.height + 'px', height: inscriptionCrop.width + 'px'});
    else
        jQuery(image).css({width: inscriptionCrop.width + 'px', height: inscriptionCrop.height + 'px'});


    if(angleRotation != 180) {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.save();
        canvas.width = image.height;
        canvas.height = image.width;

        ctx.translate(canvas.width / 2, canvas.height / 2);
        ctx.rotate(angleRotation * Math.PI / 180);
        ctx.drawImage(image, -image.width / 2, -image.height / 2);
    } else {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.save();
        canvas.width = image.width;
        canvas.height = image.height;

        ctx.translate(canvas.width / 2, canvas.height / 2);
        ctx.rotate(angleRotation * Math.PI / 180);
        ctx.drawImage(image, -image.width / 2, -image.height / 2);
    }

    image.src = canvas.toDataURL();
    ctx.restore();

    jQuery(image).show();
}

function saveCoords(c) {
    jQuery('#inscription_crop_x1').val(c.x);
    jQuery('#inscription_crop_y1').val(c.y);
    jQuery('#inscription_crop_x2').val(c.x2);
    jQuery('#inscription_crop_y2').val(c.y2);
    jQuery('#inscription_crop_width').val(c.w);
    jQuery('#inscription_crop_height').val(c.h);
}

function setAreaSelect(bEmptyArea) {
    if(typeof bEmptyArea == "undefined") {
        bEmptyArea = true;
    }

    jQuery('#inscription_crop_x1').val('');
    jQuery('#inscription_crop_y1').val('');
    jQuery('#inscription_crop_x2').val('');
    jQuery('#inscription_crop_y2').val('');
    jQuery('#inscription_crop_width').val('');
    jQuery('#inscription_crop_height').val('');

    if (inscriptionCrop.jcrop_api)
        inscriptionCrop.jcrop_api.destroy();

    inscriptionCrop.jcrop_enabled = true;

    jQuery('#crop-image-target').Jcrop({
        onChange: saveCoords,
        onSelect: saveCoords,
        onRelease: saveCoords
    }, function () {
        inscriptionCrop.jcrop_api = this;
        jQuery('#inscription_crop_originalHeight').val(jQuery('.jcrop-holder').height());
        jQuery('#inscription_crop_originalWidth').val(jQuery('.jcrop-holder').width());

        if(bEmptyArea === false) {
            var width = jQuery('.jcrop-holder').width();
            var height = jQuery('.jcrop-holder').height();

            var x1 = width * 0.10;
            var y1 = height * 0.10;
            var x2 = x1 + (width * 0.4);
            var y2 = y1 + (height*0.4);

            inscriptionCrop.jcrop_api.setSelect([x1, y1, x2, y2]);
        }
    });
}

function destroyJcrop() {
    jQuery('#inscription_crop_x1').val('');
    jQuery('#inscription_crop_y1').val('');
    jQuery('#inscription_crop_x2').val('');
    jQuery('#inscription_crop_y2').val('');
    jQuery('#inscription_crop_width').val('');
    jQuery('#inscription_crop_height').val('');

    if (inscriptionCrop.jcrop_api)
        inscriptionCrop.jcrop_api.destroy();

    inscriptionCrop.jcrop_enabled = false;
}

function cancel() {
    var canvas = document.getElementById("canvas");
    var ctx = canvas.getContext("2d");
    var image = document.getElementById("crop-image-target");

    if(inscriptionCrop.angle > 0) {
        var angleRestant = 360 - inscriptionCrop.angle;
        var nbrRotationRestante = angleRestant / 90;

        inscriptionCrop.angle = 0;
        jQuery('#inscription_crop_rotation').val(inscriptionCrop.angle);
        drawRotated(ctx, canvas, image, nbrRotationRestante);
    }

    destroyJcrop();
}

