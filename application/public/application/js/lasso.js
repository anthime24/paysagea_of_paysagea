var isLassoActive = false;

$(document).ready(function () {

    var images = [];
    var min = 99;
    var max = 999999;
    var pointRadius = 4;
    var polygonMode = true;
    var pointArray = new Array();
    var lineArray = new Array();
    var activeLine = null;
    var activeShape = false;
    var patternLoadError = 0;
    var patternScale = 1;
    var $patternImg = null;
    var entiteJson = null;

    var canvas = null;
    var generatedPolygon = null;

    var prototypefabric = new function () {
        this.initCanvas = function () {

            $('#canvasLasso').zIndex(20);
            $('#canvasLasso').closest('.canvas-container').zIndex(20);

            canvas = window._canvas = new fabric.Canvas('canvasLasso', {
                top: 0,
                left: 0
            });
            canvas.setBackgroundColor(null);


            $('#contenu').css('overflow', 'visible');
            $('#canvasLasso').closest('.canvas-container').css('top', '-27px');
            $('#canvasLasso').closest('.canvas-container').css('left', '-27px');
            $('#canvasLasso').css('border', '2px dashed grey');


            canvas.on('canvas:mousedown', function (options) {
                if (typeof options.target != "undefined" && typeof options.target.id != "undefined" && pointArray.length > 0) {
                    if (options.target && options.target.id == pointArray[0].id) {
                        ouvrirSelection('popin-lasso');
                        rechercherFormulaireSelectionLasso();
                    }
                }

                if (polygonMode) {
                    prototypefabric.polygon.addPoint(options);
                }
            });

            canvas.on('canvas:mousemove', function (options) {
                if (activeLine && activeLine.class == "line") {
                    var pointer = canvas.getPointer(options.e);
                    activeLine.set({x2: pointer.x, y2: pointer.y});

                    var points = activeShape.get("points");
                    points[pointArray.length] = {
                        x: pointer.x,
                        y: pointer.y
                    }
                    activeShape.set({
                        points: points
                    });
                    canvas.renderAll();
                }
                canvas.renderAll();
            });

            $('#contenu').on('mousemove', mouseMoveHandler);
            $('#contenu').on('mousedown', mouseDownHandler);
        };

        this.fillCanvasWithPattern = function () {


            var that = this;
            generatedPolygon = prototypefabric.polygon.generatePolygon(pointArray);
            generatedPolygon.setBackgroundColor = null;

            var patternSrc = $patternImg.attr('src');
            var patternWidth = null;
            var patternHeight = null;

            if (typeof $patternImg.attr('attr-width') != "undefined" && $patternImg.attr('attr-width').trim() != "") {
                patternWidth = $patternImg.attr('attr-width');
                patternHeight = $patternImg.attr('attr-height');
            }

            fabric.Image.fromURL(patternSrc, function (img) {
                try {
                    if (img === null) {
                        throw "Pattern not loaded";
                    }

                    var nbrLasso = 0;
                    $('#contenu').find('.entite-accroche').each(function () {
                        if (typeof $(this).attr('attr-lasso') == "undefined" && $(this).attr('attr-lasso') == 1) {
                            nbrLasso++;
                        }
                    });

                    if (nbrLasso >= 19) {
                        throw "Vous ne pouvez pas avoir plus de 19 zones à remplir";
                    }

                    img.scale(patternScale);

                    var patternSourceCanvas = new fabric.StaticCanvas();
                    patternSourceCanvas.add(img);
                    patternSourceCanvas.renderAll();

                    var pattern = new fabric.Pattern({
                        source: function () {
                            patternSourceCanvas.setDimensions({
                                width: img.getScaledWidth(),
                                height: img.getScaledHeight()
                            });
                            patternSourceCanvas.renderAll();
                            return patternSourceCanvas.getElement();
                        },
                        repeat: 'repeat'
                    });

                    generatedPolygon.set('fill', pattern);
                    canvas.renderAll();

                    return true;

                } catch (err) {
                    if (patternLoadError < 1) {
                        patternLoadError++;
                        if (typeof generatedPolygon != "undefined" && generatedPolygon !== null) {
                            canvas.remove(generatedPolygon);
                            canvas.renderAll();
                        }
                        that.fillCanvasWithPattern();
                    } else {
                        fermerSelection('popin-lasso', true);
                        stopperLasso();
                        afficherErreur("Une erreur est survenu lors du remplissage de la zone, veuillez réessayer ultiérieurement ou contacter le support technique si le problème persiste");
                    }
                }
            });
        };

        this.finalizeSelction = function () {
            generatedPolygon.toDataURL();
            var polygonCoords = generatedPolygon.aCoords;
            var generatedPolygonUrl = generatedPolygon.toDataURL();
            var idEntite = jQuery(entiteJson.maSelection).find('input.entite-id').val();

            var $contenu = jQuery('<div>' + entiteJson['contenu'] + '</div>');
            $contenu.find('img').attr('src', generatedPolygonUrl);
            $contenu.find('img').width(generatedPolygon.width);
            $contenu.find('img').height(generatedPolygon.height);
            $contenu.find('img').parent().width(generatedPolygon.width);
            $contenu.find('img').parent().height(generatedPolygon.height);

            $contenu.find('.entite-accroche').attr('attr-top', polygonCoords['tl']['y'] - 25);
            $contenu.find('.entite-accroche').attr('attr-left', polygonCoords['tl']['x'] - 25);
            $contenu.find('.entite-accroche').attr('attr-taille-fixe', "1");
            $contenu.find('.entite-accroche').attr('attr-lasso', "1");
            $contenu.find('.entite-accroche').attr('attr-envoyer-image', '1');
            entiteJson['contenu'] = $contenu.html();

            ajouterEntiteDepuisJson(idEntite, entiteJson, polygonCoords['tl']['x'] - (25 + 2), polygonCoords['tl']['y'] - (25 + 2), generatedPolygon.height, generatedPolygon, function () {
                fermerSelection('popin-lasso', true);
            });
            stopperLasso();
        }
    };

    prototypefabric.polygon = {
        drawPolygon: function () {
            polygonMode = true;
            pointArray = new Array();
            lineArray = new Array();
            activeLine;
        },
        addPoint: function (options) {
            var random = Math.floor(Math.random() * (max - min + 1)) + min;
            var id = new Date().getTime() + random;
            var circle = new fabric.Circle({
                radius: pointRadius,
                fill: '#ffffff',
                stroke: '#333333',
                strokeWidth: 0.5,
                left: (options.cursorX / canvas.getZoom()),
                top: (options.cursorY / canvas.getZoom()),
                selectable: false,
                hasBorders: false,
                hasControls: false,
                originX: 'center',
                originY: 'center',
                hoverCursor: "default",
                moveCursor: "default",
                id: id
            });
            if (pointArray.length == 0) {
                circle.set({
                    fill: 'red'
                })
            }

            var points = [(options.cursorX / canvas.getZoom()), (options.cursorY / canvas.getZoom()), (options.cursorX / canvas.getZoom()), (options.cursorY / canvas.getZoom())];
            var line = new fabric.Line(points, {
                strokeWidth: 1,
                fill: '#999999',
                stroke: '#999999',
                class: 'line',
                originX: 'center',
                originY: 'center',
                selectable: false,
                hasBorders: false,
                hasControls: false,
                evented: false
            });


            if (activeShape) {
                var pos = canvas.getPointer(options.e);
                var points = activeShape.get("points");
                points.push({
                    x: pos.x,
                    y: pos.y
                });
                var polygon = new fabric.Polyline(points, {
                    stroke: '#333333',
                    strokeWidth: 0.5,
                    fill: '#cccccc',
                    opacity: 0.3,
                    selectable: false,
                    hasBorders: false,
                    hasControls: false,
                    evented: false
                });
                canvas.remove(activeShape);
                canvas.add(polygon);
                activeShape = polygon;
                canvas.renderAll();
            } else {
                var polyPoint = [{x: (options.cursorX / canvas.getZoom()), y: (options.cursorY / canvas.getZoom())}];
                var polygon = new fabric.Polyline(polyPoint, {
                    stroke: '#333333',
                    strokeWidth: 0.5,
                    fill: '#cccccc',
                    opacity: 0.3,
                    selectable: false,
                    hasBorders: false,
                    hasControls: false,
                    evented: false
                });
                activeShape = polygon;
                canvas.add(polygon);
            }
            activeLine = line;

            pointArray.push(circle);
            lineArray.push(line);

            canvas.add(line);
            line.moveTo(99);

            canvas.add(circle);
            circle.moveTo(99);

            canvas.selection = false;
        },
        generatePolygon: function (pointArray) {
            var points = new Array();
            $.each(pointArray, function (index, point) {
                points.push({
                    x: point.left,
                    y: point.top
                });
                canvas.remove(point);
            });
            $.each(lineArray, function (index, line) {
                canvas.remove(line);
            });
            canvas.remove(activeShape).remove(activeLine);
            var polygon = new fabric.Polygon(points, {
                stroke: '#333333',
                strokeWidth: 0.5,
                opacity: 1,
                hasBorders: false,
                hasControls: false
            });
            canvas.add(polygon);
            polygon.moveTo(99);

            activeLine = null;
            activeShape = null;
            polygonMode = false;
            canvas.selection = true;

            return polygon;
        }
    };

    function mouseMoveHandler(e) {
        e.preventDefault();
        e.stopPropagation();

        var eventParameter = getEventParameters(e, pointArray);
        canvas.trigger('canvas:mousemove', eventParameter);
    }

    function mouseDownHandler(e) {
        e.preventDefault();
        e.stopPropagation();

        var eventParameter = getEventParameters(e, pointArray, true);
        canvas.trigger('canvas:mousedown', eventParameter);
    }

    function getEventParameters(e, pointArray, debugMode) {
        var target = null;
        var posX = e.pageX;
        var posY = e.pageY;

        var canvasX = jQuery('#canvasLasso').offset().left;
        var canvasY = jQuery('#canvasLasso').offset().top;
        var cursorX = posX - canvasX;
        var cursorY = posY - canvasY;

        if (pointArray.length > 0) {
            for (var i = 0; i < pointArray.length; i++) {
                var originalPoint = pointArray[i];
                var originalPointTopLeft = originalPoint.aCoords.tl;
                var originalPointBottomRight = originalPoint.aCoords.br;

                var foundX = false;
                if (cursorX >= originalPointTopLeft.x && cursorX <= originalPointBottomRight.x) {
                    foundX = true;
                }

                var foundY = false;
                if (cursorY >= originalPointTopLeft.y && cursorY <= originalPointBottomRight.y) {
                    foundY = true;
                }

                if (foundX && foundY) {
                    target = originalPoint;
                    break;
                }
            }
        }

        if (target !== null) {
            return {
                e: e,
                target: target,
                cursorX: cursorX,
                cursorY: cursorY
            };
        } else {
            return {
                e: e,
                cursorX: cursorX,
                cursorY: cursorY
            };
        }
    }

    function initialiserLasso() {
        isLassoActive = true;

        min = 99;
        max = 999999;
        polygonMode = true;
        pointArray = new Array();
        lineArray = new Array();
        activeLine = null;
        activeShape = false;
        canvas = null;
        patternLoadError = 0;

        patternScale = 1;
        $patternImg = null;
        entiteJson = null;
        generatedPolygon = null;

        var $canvasLassoTemplate = $('#canvasLassoTemplate');
        $('#contenu').append('<canvas id="canvasLasso" width="' + $canvasLassoTemplate.attr('width') + '" height="' + $canvasLassoTemplate.attr('height') + '"></canvas>');

        $('#contenu-fond-image').css('position', 'absolute');
        $('#contenu-fond-image').css('top', '0px');
        $('#contenu-fond-image').css('left', '0px');

        $('#contenu .entite-accroche').css('cursor', 'default');

        terminerContenu();
        prototypefabric.initCanvas();
        afficherAideLasso();

    }

    function stopperLasso(reInitMouseEvent) {
        isLassoActive = false;
        generatedPolygon = null;

        if (typeof (reInitMouseEvent) == 'undefined' || reInitMouseEvent !== false) {
            reInitMouseEvent = true;
        }

        $('#contenu-fond-image').find('img').css('position', 'relative');
        $('#contenu-fond-image').find('img').css('top', 'unset');
        $('#contenu-fond-image').find('img').css('left', 'unset');

        $('#contenu .entite-accroche').css('cursor', 'pointer');

        $('#canvasLasso').css('border', 'none');
        jQuery('#contenu').css('overflow', 'hidden');

        if (typeof canvas != "undefined" && canvas !== null) {
            canvas.clear();
            jQuery('.canvas-container').remove();
        }

        if ($('#popin-lasso').is(':visible')) {
            $('#popin-lasso').fadeOut('slow');
        }

        cacherAideLasso();

        if (reInitMouseEvent === true) {
            contenuMouseEnter();
            contenuMouseDown();
        }

        $('#contenu').off('mousemove', mouseMoveHandler);
        $('#contenu').off('mousedown', mouseDownHandler);
    }

    function zoomerLasso(mode) {

        var max = 2;
        var min = 0.25;
        var step = 0.25;

        if (mode == 'zoomIn') {
            if (patternScale < max) {
                patternScale = patternScale + step;
            }
        } else if (mode == 'zoomOut') {
            if (patternScale > min) {
                patternScale = patternScale - step;
            }
        }

        prototypefabric.fillCanvasWithPattern();
    }

    function centrerAideLasso() {
        var windowWidth = jQuery(window).width();
        var menuWidth = jQuery('#menu').width();
        var actionsWidth = jQuery('#lasso-help').width();

        var newMargin = parseFloat((windowWidth - actionsWidth) / 2) + parseFloat(menuWidth / 2);

        if (newMargin >= menuWidth) {
            jQuery('#lasso-help').css('margin-left', newMargin + 'px');
        }

        centrerDivVerticalement('action');
    }

    function afficherAideLasso() {
        if (jQuery('#actions').is(':visible')) {
            jQuery('#actions').hide();
        }

        centrerAideLasso();
        jQuery('#lasso-help').show();
    }

    function cacherAideLasso() {
        jQuery('#lasso-help').hide();
    }

    jQuery(document).on('click', '#menu-bloc-actions-lasso', function (e) {
        e.preventDefault();
        e.stopPropagation();

        menuItemClickedEvent(e).then(function (shouldContinue) {
            if (shouldContinue == true) {
                jQuery('body').unbind('mouseenter');
                jQuery('body').unbind('mouseleave');
                jQuery('#contenu').unbind('mousedown');

                stopperLasso(false);

                initialiserLasso();
            }
        })

        return false;
    });

    jQuery(document).on('lasso.selected-pattern', function (e, $patternImgParameter, entiteJsonParameter) {
        e.preventDefault();
        e.stopPropagation();

        $patternImg = $patternImgParameter;
        entiteJson = entiteJsonParameter;

        prototypefabric.fillCanvasWithPattern();
        fermerSelection('popin-lasso', true);
    });

    jQuery(window).resize(function () {
        centrerAideLasso();
    });

    jQuery(document).on('stropperLassoRequest', function (e) {
        stopperLasso();
    });

    jQuery('body').on('click', '.croix-ferme', function () {
        if (jQuery(this).closest('.popin').attr('id') == 'popin-lasso') {
            stopperLasso();
        }
    });

    jQuery('#lasso-help .control .item').on('click', function (e) {
        var target = $(this).attr('data-action');

        if (target == 'cancel') {
            stopperLasso();
        } else if (target == 'finalize') {
            if (generatedPolygon !== null) {
                prototypefabric.finalizeSelction();
            }
        } else if (target == 'zoomIn') {
            if (generatedPolygon !== null) {
                zoomerLasso(target);
            }
        } else if (target == 'zoomOut') {
            if (generatedPolygon !== null) {
                zoomerLasso(target);
            }
        }
    });

    jQuery(document).on('keydown', function (e) {

        if (isWaitingForIntteruptionConfirm == true) {
            return false;
        }

        var key = "";
        if (typeof e.key != "undefined") {
            key = e.key;
        }

        if (e.keyCode == 27 || key == 'Escape') {
            //stopperLasso();
        } else if (e.keyCode == 109 || key == '-') {
            if (generatedPolygon !== null) {
                zoomerLasso('zoomOut');
            }
        } else if (e.keyCode == 107 || key == '+') {
            if (generatedPolygon !== null) {
                zoomerLasso('zoomIn');
            }
        } else if (e.keyCode == 13 || key == 'Enter') {
            if (generatedPolygon !== null) {
                e.preventDefault();
                e.stopPropagation();

                prototypefabric.finalizeSelction();
                return false;
            }
        }
    });
})
