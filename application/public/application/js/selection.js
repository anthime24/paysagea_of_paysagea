function fenetreSelection(action, idPopin) {
    if (action === 'ouvrir') {
        jQuery('#' + idPopin).fadeIn();
    } else {
        jQuery('#' + idPopin).fadeOut();
    }

    if (idPopin == 'popin-lasso') {
        initialiserFenetreSelection('#popin-lasso-content-contenu-filtre');
        adaptativeSizeContent(true);
    } else {
        initialiserFenetreSelection('#popin-ajout-content-contenu-filtre');
        adaptativeSizeContent();
    }
}

function etatFenetreSelection() {
    return jQuery('#popin-ajout').css('display') === 'none' ? 'ferme' : 'ouvert';
}

function initialiserFenetreSelection(element) {
    var heightWindow = jQuery(window).height();
    var heightElement = jQuery(element).closest('.popin-content').height();

    jQuery(element).closest('.popin-content').css('margin-top', ((heightWindow / 2) - (heightElement / 2)) + 'px');
}

function ouvrirSelection(that) {
    fenetreSelection('ouvrir', that);
}

function fermerSelection(that, relative) {
    if (typeof relative == 'undefined' || relative === false) {
        fenetreSelection('fermer', jQuery(that).closest('.popin').attr('id'));
    } else {
        fenetreSelection('fermer', that);
    }
}

function basculerSelection() {
    if (etatFenetreSelection() === 'ferme')
        ouvrirSelection('popin-ajout');
    else
        fermerSelection('#popin-ajout-content-contenu-filtre');
}

function rechercherFormulaireSelection() {
    var formulaire = jQuery('#popin-ajout-content-contenu-filtre form').serialize();
    var url = jQuery('#popin-ajout-content-contenu-filtre form').attr('action');
    var type = jQuery('input[name="mjmt_appbundle_filtre_entite_type"]:checked').val();

    jQuery('input[name="mjmt_appbundle_filtre_entite_type"]:checked').attr('data-reloaded', 1);

    jQuery('#filtre-resultat-' + type).css('display', 'block');
    jQuery('#formulaire-' + type).css('display', 'block');

    bloquerFenetreSelectionEntite();
    jQuery.ajax({
        url: url,
        type: 'POST',
        data: formulaire,
        dataType: 'json'
    }).done(function (json) {
        var type = null;
        jQuery.each(json, function (key, value) {
            type = key;
            jQuery('#filtre-resultat-' + key).html(value);
            adaptativeSizeContent();
        });
        var typeForm = jQuery('#select-type-entite').val();
        // Met à jour l'affichage du nb d'entités affichées
        jQuery('#filtre-resultat-nombre-' + typeForm + ' span').html(json.nbEntitesAffichees);
        debloquerFenetreSelectionEntite();
        jQuery('#filtre-resultat').scrollTop(0);

    }).always(function () {
        var typeForm = jQuery('#select-type-entite').val();
        jQuery('#filtre-resultat-' + typeForm).removeClass('filtre-resultat-chargement');
    });
}

function rechercherFormulaireSelectionLasso() {
    var url = jQuery('#popin-lasso-content-contenu-resultat').attr('data-url');
    jQuery.ajax({
        url: url,
        type: 'GET',
        dataType: 'json'
    }).done(function (json) {

        var type = null;
        jQuery('.filtre-resultat-nombre-lasso .nbrEntitesAffichees').html(json.nbEntitesAffichees);
        jQuery('.filtre-resultat-nombre-lasso .nbrEntitesTrouvees').html(json.nbTotalResult);
        jQuery('.filtre-resultat-lasso').html(json.entiteLasso);

        adaptativeSizeContent(true);

        jQuery('#filtre-resultat-nombre-lasso').scrollTop(0);
    });
}

function afficherFormulaireSelection(type, previousType) {
    hScroll[previousType] = jQuery('#filtre-resultat').scrollTop();
    jQuery('#filtre-resultat').scrollTop(hScroll[type]);

    var entite_non_visible = previousType;
    jQuery('#selection-entite form').find('#formulaire-' + entite_non_visible).removeClass('formulaire-actif');
    jQuery('#selection-entite form').find('#formulaire-' + type).addClass('formulaire-actif');
    jQuery('#selection-entite form #formulaire-' + entite_non_visible).css('display', 'none');
    if (jQuery('#formulaire-' + jQuery('#select-type-entite').val() + ' button.plus-option').html() == '+ d\'options...')
        optionsFormulaireSelection(jQuery('#select-type-entite').val(), 'fermer');
    else
        optionsFormulaireSelection(jQuery('#select-type-entite').val(), 'ouvrir');
    jQuery('#selection-entite form #formulaire-' + type).css('display', 'block');
    jQuery('#filtre-resultat-' + entite_non_visible).css('display', 'none');
    jQuery('#filtre-resultat-' + type).css('display', 'block');
}

function optionsFormulaireSelection(entiteType, action) {
    if (action == 'ouvrir') {
        if (entiteType == 'plante') {
            jQuery('#selection-entite #mjmt_appbundle_filtre_' + entiteType).height(135);
            jQuery('#selection-entite #filtre-resultat').height(284);
        } else if (entiteType == 'decor') {
            jQuery('#selection-entite #mjmt_appbundle_filtre_' + entiteType).height(106);
            jQuery('#selection-entite #filtre-resultat').height(291);
        } else {
            jQuery('#selection-entite #mjmt_appbundle_filtre_' + entiteType).height(89);
            jQuery('#selection-entite #filtre-resultat').height(308);
        }

    } else {
        jQuery('#selection-entite #mjmt_appbundle_filtre_' + entiteType).height(33);
        jQuery('#selection-entite #filtre-resultat').height(380);
    }
    return false;
}

function etatOptionsFormulaireSelection(entiteType) {
    return jQuery('#selection-entite #formulaire-' + entiteType + ' button.plus-option').text().indexOf('+') >= 0 ? 'ferme' : 'ouvert';
}

function bloquerFenetreSelectionEntite() {
    jQuery('#filtre-resultat-blocage').css('display', 'block');
}

function debloquerFenetreSelectionEntite() {
    jQuery('#filtre-resultat-blocage').css('display', 'none');
}

function clicSelectionEntite(url, x, y, height, idInitialEntite) {
    if ((jQuery('#acces-complet-client').val() == 1) || (jQuery('#acces-complet-client').val() != 1 && jQuery('img.entite').size() < nbMaxEntiteGratuit)) {
        bloquerFenetreSelectionEntite();
        jQuery.ajax({
            url: url,
            dataType: 'json'
        }).done(function (entiteJson) {
            var idEntite = jQuery(entiteJson.maSelection).find('input.entite-id').val();
            ajouterEntiteContenu(entiteJson.contenu);
            ajouterEntiteMaSelection(entiteJson.maSelection);
            ajouterEntiteDernierArticleAjoute(entiteJson.dernierAjout);

            if (x != null && y != null)
                deplacerVersXYEntite(idEntite, x, y, height);

            if (idInitialEntite != null) {
                var entiteHtml = recupererEntite(idEntite);
                var entiteInitialEntite = recupererEntite(idInitialEntite);
                entiteHtml.attr('attr-rotation', entiteInitialEntite.attr('attr-rotation'));
                entiteHtml.attr('attr-transformation', entiteInitialEntite.attr('attr-transformation'));
                entiteHtml.css('transform', 'rotate(' + entiteInitialEntite.attr('attr-rotation') + 'deg)');
                entiteHtml.find('div[id^="entite-photo-position"]').css('transform', entiteInitialEntite.attr('attr-transformation'));
            }

            //Si pas d'attente de validation d'action on sélectionne la nouvelle entité sinon on la met en arriere plan
            if (!estAValider())
                clickEntiteMaSelection(idEntite, true);
            else
                jQuery('#contenu div#entite-photo-' + idEntite).addClass('entite-en-arriere');

            fermerSelection('#popin-ajout-content-contenu-filtre');
            debloquerFenetreSelectionEntite();
//            if (jQuery('#bloc-ma-selection').css('width') === '0px')
//                toggleMaSelection();
            updateTotalPrice();
            updateNumberSelection();

//            setTimeout(function () {
//                toggleMaSelection();
//            }, 3000);
        }).fail(function () {
            debloquerFenetreSelectionEntite();
        });
    } else {
        jQuery('#selection-entite-erreur-maximum').fadeIn();
        centrerDiv('fenetre-info-selection-entite');
        jQuery('#selection-entite-erreur-maximum #confirmation-oui').click(function () {
            jQuery('#selection-entite-erreur-maximum').fadeOut();
            sauvegarderCreation(jQuery('#menu-sauvegarder-creation a').attr('href'), false, false, false, false, '', '');
            jQuery(this).unbind("click");
        });
        jQuery('#selection-entite-erreur-maximum #confirmation-non').click(function () {
            toggleEntiteErreurMaximum();
        });
    }
}

function ajouterEntiteDepuisJson(idEntite, entiteJson, x, y, height, fabricJsObject, closePopinCallback) {

    if (typeof closePopinCallback != 'undefined' && closePopinCallback !== null) {
        closePopinCallback();
    }

    if (typeof fabricJsObject != 'undefined' && fabricJsObject !== null) {
        var zindexLasso = trouverZindexLasso();
        var entiteId = ajouterEntiteContenu(entiteJson.contenu, false);
        var width = jQuery('#' + entiteId).find('img').width();
        var height = jQuery('#' + entiteId).find('img').height();
        var canvasElement = fabricJsObject.toCanvasElement();

        jQuery('#' + entiteId).find('img').before(canvasElement);
        jQuery('#' + entiteId).find('canvas').width(width);
        jQuery('#' + entiteId).find('canvas').height(height);
        jQuery('#' + entiteId).find('img').remove();

        jQuery('#' + entiteId).attr('attr-zindex', zindexLasso);
        jQuery('#' + entiteId).css('z-index', zindexLasso);
    } else {
        ajouterEntiteContenu(entiteJson.contenu);
    }

    ajouterEntiteMaSelection(entiteJson.maSelection, true);
    ajouterEntiteDernierArticleAjoute(entiteJson.dernierAjout);

    if (x != null && y != null) {
        deplacerVersXYEntite(idEntite, x, y, height, false);
    }

    clickEntiteMaSelection(idEntite, true);

    fermerSelection('#popin-ajout-content-contenu-filtre');
    debloquerFenetreSelectionEntite();
    updateTotalPrice();
    updateNumberSelection();
}

function reinitialiserFormulaireSelection() {
    jQuery('.formulaire-actif input[type=text]').each(function () {
        if (jQuery(this).attr('attr-default')) {
            jQuery(this).val(jQuery(this).attr('attr-default'));
        } else {
            jQuery(this).val('');
        }
    });

    jQuery('.formulaire-actif select').each(function () {
        if (jQuery(this).attr('attr-default')) {
            var multipleValues = getSelectArrayValues(jQuery(this).attr('attr-default'));

            if (typeof jQuery(this).attr('multiple') != "undefined") {
                if (multipleValues.length == 0) {
                    jQuery(this).find('option').prop("selected", false);
                } else {
                    jQuery(this).find('option').prop("selected", false);
                    for (var i = 0; i < multipleValues.length; i++) {
                        jQuery(this).find('option[value="' + multipleValues[i] + '"]').prop("selected", true);
                    }
                }
            } else {
                jQuery(this).val(jQuery(this).attr('attr-default'));
            }
        } else {
            jQuery(this).val('');
        }

        if (jQuery(this).hasClass('multipleSelectHolder')) {
            jQuery(this).trigger('refreshMultipleSelect');
        }
    });
    jQuery('.formulaire-actif input[type=checkbox]').each(function () {
        if (jQuery('#type-creation').val() == 'Terrasse') {
            jQuery(this).prop('checked', true);
            if (jQuery('.formulaire-actif').attr('id') == 'formulaire-plante') {
                jQuery('#mjmt_appbundle_filtre_plante_arrosage').val(0);
                updateFiltreArrosagePlante();
            } else if (jQuery('.formulaire-actif').attr('id') == 'formulaire-decor') {
                jQuery('#mjmt_appbundle_filtre_decor_arrosage').val(0);
                updateFiltreArrosageDecor();
            }
        } else {
            jQuery(this).removeAttr('checked');
            if (jQuery('.formulaire-actif').attr('id') == 'formulaire-plante') {
                jQuery('#mjmt_appbundle_filtre_plante_arrosage').val(1);
                updateFiltreArrosagePlante();
            } else if (jQuery('.formulaire-actif').attr('id') == 'formulaire-decor') {
                jQuery('#mjmt_appbundle_filtre_decor_arrosage').val(1);
                updateFiltreArrosageDecor();
            }
        }

    });
}

function toggleAvertissementRegleArrosage(arrosageDeuxFoisSemaine) {
    var isDisplayed = jQuery('#avertissement-regle-arrosage').is(':visible');

    var willBeDisplayed = null;
    if(arrosageDeuxFoisSemaine == true) {
        jQuery('#avertissement-regle-arrosage').css('display', 'none');
        willBeDisplayed = false;
    } else {
        jQuery('#avertissement-regle-arrosage').css('display', 'block');
        willBeDisplayed = true;
    }

    if(isDisplayed != willBeDisplayed) {
        adaptativeSizeContent();
    }
}

function updateDefaultSetting(soleil, typeCreation, style, arrosageDeuxFoisSemaine) {

    if(arrosageDeuxFoisSemaine == true) {
        jQuery('#marque_blanche_appbundle_filtre_plante_arrosage').prop('checked', true);
        jQuery('#marque_blanche_appbundle_filtre_decor_arrosage').prop('checked', true);
    } else {
        jQuery('#marque_blanche_appbundle_filtre_plante_arrosage').prop('checked', false);
        jQuery('#marque_blanche_appbundle_filtre_decor_arrosage').prop('checked', false);
    }
    toggleAvertissementRegleArrosage(jQuery('#marque_blanche_appbundle_filtre_plante_arrosage').is(':checked'));

    jQuery('#marque_blanche_appbundle_filtre_plante_ensoleillement').attr('attr-default', soleil);
    jQuery('#marque_blanche_appbundle_filtre_plante_ensoleillement').val(soleil);
    jQuery('#marque_blanche_appbundle_filtre_decor_ensoleillement').attr('attr-default', soleil);
    jQuery('#marque_blanche_appbundle_filtre_decor_ensoleillement').val(soleil);
    jQuery('#marque_blanche_appbundle_filtre_amenagement_style').attr('attr-default', style);
    jQuery('#marque_blanche_appbundle_filtre_amenagement_style').val(style);
    jQuery('#marque_blanche_appbundle_filtre_decoration_style').attr('attr-default', style);
    jQuery('#marque_blanche_appbundle_filtre_decoration_style').val(style);
    jQuery('#marque_blanche_appbundle_filtre_decor_style').attr('attr-default', style);
    jQuery('#marque_blanche_appbundle_filtre_decor_style').val(style);

    $('#popin-ajout-content-contenu-filtre-gauche').find('input[type="radio"]').each(function () {
        $(this).attr('data-reloaded', 0);
    });

//    verifierFiltreDefaut();
    rechercherFormulaireSelection();

    jQuery('#type-creation').val(typeCreation);
    if (jQuery('#marque_blanche_appbundle_filtre_decor_arrosage').val(0))
        jQuery('#marque_blanche_appbundle_filtre_decor_arrosage').val(1);
    else {
        jQuery('#marque_blanche_appbundle_filtre_decor_arrosage').val(0);
    }

    if (jQuery('#marque_blanche_appbundle_filtre_plante_arrosage').val(0))
        jQuery('#marque_blanche_appbundle_filtre_plante_arrosage').val(1);
    else {
        jQuery('#marque_blanche_appbundle_filtre_plante_arrosage').val(0);
    }

    if (typeCreation == 1) {
        var valeurArrosageTypeCreation = $('#marque_blanche_appbundle_filtre_decor_arrosageDeuxFoisParSemaineJardin').val();
    } else if (typeCreation == 2) {
        var valeurArrosageTypeCreation = $("#marque_blanche_appbundle_filtre_decor_arrosageDeuxFoisParSemaineTerrasse").val();
    }


    if (typeCreation == "2" && valeurArrosageTypeCreation.trim() == "") {
        $('#marque_blanche_appbundle_filtre_plante_arrosage').prop("checked", true);
        $('#marque_blanche_appbundle_filtre_decor_arrosage').prop("checked", true);
    } else {
        if (valeurArrosageTypeCreation.trim() == "1") {
            $('#marque_blanche_appbundle_filtre_plante_arrosage').prop("checked", true);
            $('#marque_blanche_appbundle_filtre_decor_arrosage').prop("checked", true);
        } else {
            $('#marque_blanche_appbundle_filtre_plante_arrosage').prop("checked", false);
            $('#marque_blanche_appbundle_filtre_decor_arrosage').prop("checked", false);
        }
    }

    updateFiltreArrosageDecor();
    updateFiltreArrosagePlante();

}

var loadResultat = false;
var loadResultatLasso = false;

var hScroll = [];
var nbResultatsParPage = 16;
var nbResultatsParPageLasso = 16;

var nbMaxEntiteGratuit = 5; //nombre maximum d'entite possible en mode gratuit


var animationEnCours = false;
jQuery(document).ready(function () {

    // Initialisation de la hauteur des scroll pour les remettres
    hScroll = [];
    animationEnCours = false;

    // Initialisation du top
    initialiserFenetreSelection('#popin-ajout-content-contenu-filtre');

    // Initialisation du centrage de la div
    centrerDivHorizontal('selection-entite');

    // Animation de descente de la fenêtre de sélection d'un élément
    jQuery('body').on('click', '#menu-ajouter-entite', function (e) {

        var $that = jQuery(this);

        menuItemClickedEvent(e).then(function (shouldContinue) {
            if (shouldContinue == true) {
                jQuery('#menu-bloc-actions').trigger('menu-item-clicked', [e]);

                if ($that.attr('attr-data-loaded') < 1) {
                    rechercherFormulaireSelection();
                    $that.attr('attr-data-loaded', 1);
                } else {
                    debloquerFenetreSelectionEntite();
                }

                ouvrirSelection('popin-ajout');
            }
        })
        return false;

    });

    // Animation de montée de la fenêtre de sélection d'un élément
    jQuery('body').on('click', '.croix-ferme', function () {
        fermerSelection(this);

        if (jQuery(this).closest('.popin').attr('id') === 'popin-aide') {
            callPlayer(jQuery('.mjmt-app-aide-video.current').find('iframe').attr('id'), function () {
                callPlayer(jQuery('.mjmt-app-aide-video.current').find('iframe').attr('id'), "stopVideo");
            });
        }

        return false;
    });

    // Animation lors du clic sur le bouton de + ou - d'options dans les filtres
    jQuery('body').on('click', 'button.plus-option', function () {
        var entiteType = jQuery('#selection-entite #select-type-entite').val();
        if (etatOptionsFormulaireSelection(entiteType) == 'ferme') {
            jQuery(this).html(jQuery(this).text().replace('+', '-'));
            optionsFormulaireSelection(entiteType, 'ouvrir');
        } else {
            jQuery(this).html(jQuery(this).text().replace('-', '+'));
            optionsFormulaireSelection(entiteType, 'fermer');
        }
        return false;
    });

    // Validation du formulaire de filtres et recherche des entitées
    jQuery('body').on('click', 'input.valider-form', function () {
        rechercherFormulaireSelection();

        return false;
    });

    // Réinitialisation du formulaire
    jQuery('body').on('click', '#boutton-reinitialiser', function () {
        reinitialiserFormulaireSelection();
        return false;
    });

    // On cache les informations des autres onglets
    jQuery('body').on('click', 'input[name="mjmt_appbundle_filtre_entite_type"]', function () {
        var val = jQuery(this).val();

        jQuery('#filtre-resultat-info > p').css('display', 'none');
        jQuery('#mjmt_appbundle_filtre_arrosage_message_' + val).css('display', 'block');
        jQuery('#filtre-resultat-nombre-' + val).css('display', 'block');

        jQuery('#filtre-resultat > div').css('display', 'none');
        jQuery('#filtre-resultat-' + val).css('display', 'block');

        //Si première ouverture de l'onglet, on lance une recherche
        if (jQuery('#filtre-resultat-' + val).children().length == 0) {
            rechercherFormulaireSelection();
        } else if ($(this).attr('data-reloaded') == 0) {
            rechercherFormulaireSelection();
        }
    });

    // Lors du clic sur une entité, on l'ajoute à l'espace de travail (HTML) et on l'ajoute ensuite à ma sélection
    jQuery('body').on('click', '.resultat-entite-ajout', function () {
        var url = jQuery(this).attr('href');

        if (jQuery(this).closest('.resultat-entite').parent().hasClass('filtre-resultat-lasso')) {
            if ((jQuery('#acces-complet-client').val() == 1) || (jQuery('#acces-complet-client').val() != 1 && jQuery('img.entite').size() < nbMaxEntiteGratuit)) {
                bloquerFenetreSelectionEntite();

                jQuery.ajax({
                    type: 'GET',
                    url: url,
                    dataType: 'json'
                }).done(function (entiteJson) {
                    var $contenuLasso = jQuery(entiteJson.contenu);
                    var $lassoImage = $contenuLasso.find('img');

                    $(document).trigger('lasso.selected-pattern', [$lassoImage, entiteJson]);
                }).fail(function () {
                    debloquerFenetreSelectionEntite();
                })

            } else {
                jQuery('#selection-entite-erreur-maximum').fadeIn();
                centrerDiv('fenetre-info-selection-entite');
                jQuery('#selection-entite-erreur-maximum #confirmation-oui').click(function () {
                    jQuery('#selection-entite-erreur-maximum').fadeOut();
                    sauvegarderCreation(jQuery('#menu-sauvegarder-creation a').attr('href'), false, false, false, false, '', '');
                    jQuery(this).unbind("click");
                });
                jQuery('#selection-entite-erreur-maximum #confirmation-non').click(function () {
                    toggleEntiteErreurMaximum();
                });
            }
        } else {
            var url = jQuery(this).attr('href');
            clicSelectionEntite(url);
        }

        return false;
    });

    // Lors du clic sur une entité, on l'ajoute à l'espace de travail (HTML) et on l'ajoute ensuite à ma sélection
    jQuery('body').on('click', '.resultat-entite-ajout-indisponible', function () {
        toggleArticleIndisponible();
        var lienFinaliser = jQuery('#url-finalisation-creation-compte').val() + '?redirectToOfferSelection=true';

        jQuery('#popin-article-indisponible #popin-article-indisponible-ok').click(function () {
            toggleArticleIndisponible();
            jQuery(this).unbind("click");
            jQuery('#popin-article-indisponible #popin-article-indisponible-ko').unbind("click");
            sauvegarderCreation(
                jQuery('#menu-bloc-actions-enregistrer a').attr('attr-href'),
                true,
                false,
                false,
                true,
                '',
                lienFinaliser);
        });

        jQuery('#popin-article-indisponible #popin-article-indisponible-ko').click(function () {
            toggleArticleIndisponible();
            jQuery(this).unbind("click");
            jQuery('#popin-article-indisponible #popin-article-indisponible-ok').unbind("click");
        });

        return false;
    });

    // Le centrage des popin se fait lors des resizes
    jQuery(window).resize(function () {
        initialiserFenetreSelection('#popin-ajout-content-contenu-filtre');
        initialiserFenetreSelection('#popin-infos-contenu');
        initialiserFenetreSelection('#popin-confirmation-contenu');
        initialiserFenetreSelection('#popin-article-indisponible-contenu');
        initialiserFenetreSelection('#popin-enregistrement-contenu');
        initialiserFenetreSelection('#popin-aide-contenu');
        initialiserFenetreSelection('#popin-finaliser-contenu');
        initialiserFenetreSelection('#popin-parametrage-contenu');
        initialiserFenetreSelection('#popin-rendu-contenu');
        initialiserFenetreSelection('#popin-rendu-contenu');
        initialiserFenetreSelection('#popin-selection-entite-erreur-maximum-contenu');
        adaptativeSizeContent();
        adaptativeSizeContentDescription();
        adaptativeSizeContentAide();
        adaptativeSizeContentFinaliser();

        if (etatFenetreSelection() == 'ouvert')
            centrerDiv('selection-entite');
        else
            centrerDivHorizontal('selection-entite');
    });

    // Scroll infini
    jQuery('#filtre-resultat').scroll(function () {
        var type = jQuery('input[name="mjmt_appbundle_filtre_entite_type"]:checked').val();
        var tailleGlobalResultat = jQuery('#filtre-resultat').get(0).scrollHeight;
        var offsetTopCourant = jQuery('#filtre-resultat').scrollTop();
        var nbResultatsCourant = jQuery('#filtre-resultat-' + type + ' .resultat-entite').size();
        var tailleResultat = jQuery('#filtre-resultat').height();

        var totalSize = offsetTopCourant + tailleResultat;
        var allowedthreshold = 20;

        if ((tailleGlobalResultat - totalSize) <= allowedthreshold
            && loadResultat == false
            && (nbResultatsCourant % nbResultatsParPage) == 0) {
            loadResultat = true;
//            jQuery('#filtre-resultat-' + type).append('<div class="filtre-resultat-chargement-contenu"></div>');
            bloquerFenetreSelectionEntite();
            jQuery.ajax({
                url: jQuery('#popin-ajout-content-contenu-filtre form').attr('action'),
                type: 'get',
                data: 'offset=' + nbResultatsCourant + '&type=' + type,
                success: function (data) {
                    var shouldAppendResult = true;
                    if (data['nbEntiteRecuperes'] == 0 && nbResultatsCourant > 0) {
                        shouldAppendResult = false;
                    }

                    if (shouldAppendResult) {
                        jQuery.each(data, function (key, value) {
                            jQuery('#filtre-resultat-' + key + ' .resultat-entite:last').after(value);
                        });
                    }
                    loadResultat = false;
                },
                complete: function () {
//                    jQuery('.filtre-resultat-chargement-contenu').remove();
                    debloquerFenetreSelectionEntite();
                }
            });
        }
    });

    jQuery('.filtre-resultat-lasso').scroll(function () {
        var tailleGlobalResultat = jQuery('.filtre-resultat-lasso').get(0).scrollHeight;
        var offsetTopCourant = jQuery('.filtre-resultat-lasso').scrollTop();
        var nbResultatsCourant = jQuery('.filtre-resultat-lasso .resultat-entite').size();
        var tailleResultat = jQuery('.filtre-resultat-lasso').height();

        var totalSize = offsetTopCourant + tailleResultat;
        var allowedthreshold = 20;

        if ((tailleGlobalResultat - totalSize) <= allowedthreshold
            && loadResultatLasso == false
            && (nbResultatsCourant % nbResultatsParPageLasso) == 0) {

            loadResultatLasso = true;
//            jQuery('#filtre-resultat-' + type).append('<div class="filtre-resultat-chargement-contenu"></div>');
            //bloquerFenetreSelectionEntite();
            jQuery.ajax({
                url: jQuery('#popin-lasso-content-contenu-resultat').attr('data-url'),
                type: 'get',
                data: 'offset=' + nbResultatsCourant,
                dataType: 'json',
                success: function (data) {
                    jQuery('.filtre-resultat-nombre-lasso .nbrEntitesAffichees').html(data.nbEntitesAffichees);
                    jQuery('.filtre-resultat-nombre-lasso .nbrEntitesTrouvees').html(data.nbTotalResult);

                    var appendResult = true;
                    var tmp = '<div>' + data.entiteLasso + '</div>';
                    if (nbResultatsCourant > 0 && jQuery(tmp).find('.resultat-entite-message-aucun-resultat').length > 0) {
                        appendResult = false;
                    }

                    if (appendResult) {
                        jQuery('.filtre-resultat-lasso .resultat-entite:last').after(data.entiteLasso);
                    }
                    loadResultatLasso = false;
                },
                complete: function () {
//                    jQuery('.filtre-resultat-chargement-contenu').remove();
                    //debloquerFenetreSelectionEntite();
                }
            });
        }
    })

    jQuery('body').on('keydown', '#filtre-formulaire input, #filtre-formulaire select', function (e) {
        switch (e.keyCode) {
            case 13:
                rechercherFormulaireSelection();
                break;
        }
    });


    // Listener les actions possibles sur une entite
    jQuery('body').on('keydown', '', function (e) {
        switch (e.keyCode) {
            case 27:
                fermerSelection('#popin-ajout-content-contenu-filtre');
                break;
        }
    });

    jQuery('#mjmt_appbundle_filtre_decor_arrosage').change(function () {
        updateFiltreArrosageDecor();
    });

    jQuery('#mjmt_appbundle_filtre_plante_arrosage').change(function () {
        updateFiltreArrosagePlante();
    });

    jQuery('body').on('change', '#mjmt_appbundle_filtre_plante select', function () {
        verifierFiltreDefaut();
    });

    jQuery('body').on('change', 'input[name="mjmt_appbundle_filtre_entite_type"]', function () {
        var value = jQuery(this).val();

        jQuery('#popin-ajout-content-contenu-filtre-centre > div').removeClass('formulaire-actif');
        jQuery('#popin-ajout-content-contenu-filtre-centre > div').css('display', 'none');
        jQuery('#formulaire-' + value).addClass('formulaire-actif');
        jQuery('#formulaire-' + value).css('display', 'block');
        toggleCriteres('');
    });

    adaptativeSizeContent();

    if (jQuery('#filtre-resultat-' + jQuery('input[name="mjmt_appbundle_filtre_entite_type"]:checked').val()).children().length == 0) {
        rechercherFormulaireSelection();
    }

    jQuery('body').on('click', '#popin-aide-fermer', function () {
        callPlayer(jQuery('.mjmt-app-aide-video.current').find('iframe').attr('id'), function () {
            callPlayer(jQuery('.mjmt-app-aide-video.current').find('iframe').attr('id'), "stopVideo");
        });

        toggleAide("close");
    });

    jQuery('#marque_blanche_appbundle_filtre_decor_arrosage').on('change', function () {
        updateFiltreArrosageDecor();
    });

    jQuery('#marque_blanche_appbundle_filtre_plante_arrosage').on('change', function () {
        updateFiltreArrosagePlante();
    });

    jQuery('#avertissement-regle-arrosage .configurerRegleArrosage').on('click', function(e){
        e.preventDefault();
        e.stopPropagation();

        fenetreSelection('fermer', 'popin-ajout');
        jQuery('#menu-parametrage-creation').trigger('click');
    })
});

function adaptativeSizeContent(lassoEntiteSize) {

    if (typeof lassoEntiteSize == 'undefined' || lassoEntiteSize === null) {
        var heightPopin = jQuery('#popin-ajout-content-contenu-filtre').closest('.popin-content').height();
        var heightTitre = jQuery('#popin-ajout-content-contenu-filtre').closest('.popin-content').find('.popin-content-titre').height();
        var heightFiltre = jQuery('#popin-ajout-content-contenu-filtre').height();
        var heightResultatTitre = jQuery('#popin-ajout-titre').height();

        var heightAvertissementArrosage = 0;
        if(jQuery('#avertissement-regle-arrosage').is(':visible')) {
            heightAvertissementArrosage = jQuery('#avertissement-regle-arrosage').height() + 5;
        }

        var newHeight = heightPopin - heightTitre - heightFiltre - heightResultatTitre - heightAvertissementArrosage;

        jQuery('#popin-ajout #filtre-resultat').css('height', (newHeight - 100) + 'px');

        var widthEntity = jQuery('#popin-ajout .resultat-entite').width();
        var newWidthOver = (5 / 100) * widthEntity;

        jQuery('#popin-ajout .resultat-entite-grisage-non-gratuit').css('left', (newWidthOver / 2) + 'px');
    } else {
        var heightPopin = jQuery('#popin-lasso').height();
        var heightTitre = jQuery('#popin-lasso .popin-content-titre').height();
        var heightFiltre = 0;
        var heightResultatTitre = jQuery('#popin-lasso .filtre-resultat-nombre-lasso').height();
        var newHeight = heightPopin - heightTitre - heightFiltre - heightResultatTitre;

        jQuery('#popin-lasso .filtre-resultat-lasso').css('height', (newHeight - 150) + 'px');

        var widthEntity = jQuery('#popin-lasso .filtre-resultat-lasso .resultat-entite').width();
        var newWidthOver = (5 / 100) * widthEntity;

        jQuery('#popin-lasso .filtre-resultat-lasso .resultat-entite-grisage-non-gratuit').css('left', (newWidthOver / 2) + 'px');
    }
}

function verifierFiltreDefaut() {
    var defaultDifferent = false;
    jQuery('#mjmt_appbundle_filtre_plante select').each(function () {
        if (jQuery(this).attr('attr-default')) {
            var valueDefault = getSelectArrayValues(jQuery(this).attr('attr-default'));
            var valueSelection = getSelectArrayValues(jQuery(this).val());

            if (compareSelectValues(valueDefault) === true && valueSelection.length > 0) {
                defaultDifferent = true;
            }
        }
    });
    if (defaultDifferent) {
        jQuery('#mjmt_appbundle_filtre_defaut_message_plante').css('display', 'block');
    } else {
        jQuery('#mjmt_appbundle_filtre_defaut_message_plante').css('display', 'none');
    }
}

function updateFiltreArrosagePlante() {
    if (jQuery('#type-creation').val() == 'Terrasse') {
        if (jQuery('#marque_blanche_appbundle_filtre_plante_arrosage').val() == 0) {
            jQuery('#mjmt_appbundle_filtre_arrosage_message_plante').css('display', 'none');
            jQuery('#marque_blanche_appbundle_filtre_plante_arrosage').val(1);
        } else {
            jQuery('#mjmt_appbundle_filtre_arrosage_message_plante').css('display', 'block');
            jQuery('#marque_blanche_appbundle_filtre_plante_arrosage').val(0);
        }
    }
    if (jQuery('#marque_blanche_appbundle_filtre_plante_arrosage').is(':checked')) {
        jQuery('#marque_blanche_appbundle_filtre_plante_eau').val('');
        jQuery('#marque_blanche_appbundle_filtre_plante_eau').prop('disabled', true);
    } else {
        jQuery('#marque_blanche_appbundle_filtre_plante_eau').prop('disabled', false);
    }
//    verifierFiltreDefaut();
}

function updateFiltreArrosageDecor() {
    if (jQuery('#type-creation').val() == 'Terrasse') {
        if (jQuery('#marque_blanche_appbundle_filtre_decor_arrosage').val() == 0) {
            jQuery('#mjmt_appbundle_filtre_arrosage_message_decor').css('display', 'none');
            jQuery('#marque_blanche_appbundle_filtre_decor_arrosage').val(1);
        } else {
            jQuery('#mjmt_appbundle_filtre_arrosage_message_decor').css('display', 'block');
            jQuery('#marque_blanche_appbundle_filtre_decor_arrosage').val(0);
        }
    }
}

function toggleCriteres(that) {
    if (jQuery('#popin-ajout-content-contenu-filtre form').height() >= 87 || that === '') {
        jQuery('#popin-ajout-content-contenu-filtre form').css('height', '85px');
        jQuery('#link-toggle-critere').html(translateJs('modal.plusCritere'));
    } else {
        jQuery('#popin-ajout-content-contenu-filtre form').css('height', '100%');
        jQuery('#link-toggle-critere').html(translateJs('modal.moinsCritere'));
    }

    var heightNew = jQuery('#popin-ajout-content-contenu-filtre form').css('height');
    jQuery('#popin-ajout-content-contenu-filtre-gauche').css('height', heightNew);
    jQuery('#popin-ajout-content-contenu-filtre-droite').css('height', heightNew);
    var widthCheckbox = jQuery('#popin-ajout-content-contenu-filtre form').width() - jQuery('#popin-ajout-content-contenu-filtre-gauche').width() - jQuery('#popin-ajout-content-contenu-filtre-droite').width();
    jQuery('#popin-ajout-content-contenu-filtre form .checkbox').parent().css('width', (widthCheckbox - 25) + 'px');

    adaptativeSizeContent();

    return false;
}

function reinitialiserFilter() {
    reinitialiserFormulaireSelection();
//    jQuery('#popin-ajout-content-contenu-filtre form #popin-ajout-content-contenu-filtre-centre input[type="text"]').val('');
//    jQuery('#popin-ajout-content-contenu-filtre form #popin-ajout-content-contenu-filtre-centre input[type="radio"]').prop('checked', false);
//    jQuery('#popin-ajout-content-contenu-filtre form #popin-ajout-content-contenu-filtre-centre input[type="checkbox"]').prop('checked', false);
//    jQuery('#popin-ajout-content-contenu-filtre form #popin-ajout-content-contenu-filtre-centre select').val('');

    return false;
}

function adaptativeSizeContentDescription() {
    var heightPopin = jQuery('#popin-informations-entite .popin-content').height();
    var heightTitre = jQuery('#popin-informations-entite .popin-content-titre').height();
    var newHeight = heightPopin - heightTitre;

    jQuery('#popin-informations-entite #popin-infos-contenu').css('height', (newHeight - 50) + 'px');
}

function openPopinInfosEntite(that) {
    var url = jQuery(that).attr('href');

    jQuery.ajax({
        type: 'GET',
        url: url
    }).done(function (html) {
        jQuery('#popin-informations-entite').html(html);
        jQuery('#popin-informations-entite').css('display', 'block');

        initialiserFenetreSelection('#popin-infos-contenu');
        adaptativeSizeContentDescription();

        //On définit la largeur du conteneur des thumbnails
        jQuery('div.description-texte-photos-apercu-conteneur img').load(function () {
            var tmpLargeur = 0;
            jQuery('div.description-texte-photos-apercu-conteneur img').each(function () {
                tmpLargeur += this.width + 4;
            });
            jQuery('.description-texte-photos-apercu-conteneur').css('width', tmpLargeur);
            jQuery('.description-texte-photos-apercu-conteneur').css('display', 'block');
        });
        jQuery('div.description-texte-photos-apercu').animate({
            scrollLeft: '=0'
        }, 400, 'easeOutQuad');
    });

    return false;
}

function adaptativeSizeContentAide() {
    var heightPopin = jQuery('#popin-aide .popin-content').height();
    var heightTitre = jQuery('#popin-aide .popin-content-titre').height();
    var newHeight = heightPopin - heightTitre;

    jQuery('#popin-aide #popin-aide-contenu').css('height', (newHeight - 90) + 'px');
}

function updateNumberSelection() {
    var number = jQuery('#conteneur-entites .selection-entite').length;

    jQuery('#menu-ma-selection-titre .badge').html(number);
}

function updateTotalPrice() {
    var tarif = 0;
    jQuery('#conteneur-entites .selection-entite-prix').each(function () {
        tarif += parseFloat(jQuery(this).val());
    });

    jQuery('#menu-lien-liste-selection span').html(tarif.toFixed(2) + ' €');
}

function toggleArticleIndisponible() {
    if (jQuery('#popin-article-indisponible').css('display') === 'none')
        jQuery('#popin-article-indisponible').fadeIn();
    else
        jQuery('#popin-article-indisponible').fadeOut();

    initialiserFenetreSelection('#popin-article-indisponible-contenu');
}

function toggleEnregistrement() {
    if (jQuery('#popin-enregistrement').css('display') === 'none')
        jQuery('#popin-enregistrement').fadeIn();
    else
        jQuery('#popin-enregistrement').fadeOut();

    initialiserFenetreSelection('#popin-enregistrement-contenu');
}

function toggleAide(mode) {
    if (mode == "open") {
        jQuery('#popin-aide').fadeIn();
    } else if (mode == "close") {
        jQuery('#popin-aide').fadeOut();
    }

    initialiserFenetreSelection('#popin-aide-contenu');
    adaptativeSizeContentAide();
}

function toggleParametrage() {
    if (jQuery('#popin-parametrage').css('display') === 'none')
        jQuery('#popin-parametrage').fadeIn();
    else
        jQuery('#popin-parametrage').fadeOut();

    initialiserFenetreSelection('#popin-parametrage-contenu');
}

function toggleRendu() {
    if (jQuery('#popin-rendu').css('display') === 'none')
        jQuery('#popin-rendu').fadeIn();
    else
        jQuery('#popin-rendu').fadeOut();

    initialiserFenetreSelection('#popin-rendu-contenu');
}

function toggleEntiteErreurMaximum() {
    if (jQuery('#selection-entite-erreur-maximum').css('display') === 'none')
        jQuery('#selection-entite-erreur-maximum').fadeIn();
    else
        jQuery('#selection-entite-erreur-maximum').fadeOut();

    initialiserFenetreSelection('#popin-selection-entite-erreur-maximum-contenu');
}
