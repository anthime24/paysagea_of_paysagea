function masquerMaSelection() {
    jQuery('#selection').css('display', 'none');
}

function afficherMaSelection() {
    jQuery('#selection').css('display', 'block');
}

function fenetreMaSelection(action) {
    if (action == 'ouvrir') {
        if (jQuery('#selection #titre-selection').addClass('ouvert'))
            jQuery('#selection #titre-selection').addClass('ouvert');
        jQuery('#selection').animate({
            right: 0
        }, 500);
    } else {
        jQuery('#selection #titre-selection').removeClass('ouvert');
        jQuery('#selection').animate({
            right: -jQuery('#conteneur-entites').width()
        }, 500);
    }
}

function etatFenetreMaSelection() {
    return jQuery('#selection').css('right') === '-' + jQuery('#conteneur-entites').width() + 'px' ? 'ferme' : 'ouvert';
}

function ouvrirMaSelection() {
    fenetreMaSelection('ouvrir');
}

function fermerMaSelection() {
    fenetreMaSelection('fermer');
}

function basculerMaSelection() {
    if (etatFenetreMaSelection() == 'ferme')
        ouvrirMaSelection();
    else
        fermerMaSelection();
}

function ouvrirPopinDescription(html) {
    jQuery('#popin-ma-selection-description').html(html);
    jQuery('#popin-ma-selection-description').fadeIn();
    // Positionnnement de départ des PopIn au centre de la page
    centrerDiv('fenetre-ma-selection-description');

    //On définit la largeur du conteneur des thumbnails
    $('div.description-texte-photos-apercu-conteneur img').load(function () {
        var tmpLargeur = 0;
        $('div.description-texte-photos-apercu-conteneur img').each(function () {
            tmpLargeur += this.width + 4;
        });
        jQuery('.description-texte-photos-apercu-conteneur').css('width', tmpLargeur);
        jQuery('.description-texte-photos-apercu-conteneur').css('display', 'block');
    });
    $('div.description-texte-photos-apercu').animate({
        scrollLeft: '=0'
    }, 400, 'easeOutQuad');

}

function fermerPopinDescription() {
    jQuery('#popin-ma-selection-description').fadeOut();
}

function ajouterEntiteMaSelection(html, bInLasso) {
    if(typeof(bInLasso) == "undefined") {
        bInLasso = false;
    }

    if(bInLasso === false) {
        $htmlParsed = jQuery('<div>' +  html + '</div>');
        $htmlParsed.find('.hiddenIfLasso').show();
        html = $htmlParsed.html();
    }

    jQuery('#conteneur-entites').prepend(html);
    var idEntite = jQuery(html).find('input.entite-id').val();

    // Initialisation des tipsy
    if (!isTouchDevice()) {
        jQuery('.ma-selection-tipsy').tipsy({
            gravity: 'e',
            opacity: 1
        });
    }
    updateTotalPrixSelection();
}

function ajouterEntiteDernierArticleAjoute(html) {
    jQuery('#menu-dernier-article-ajoute').html(html);
}

function terminerMaSelection() {
    jQuery('div.selection-entite').removeClass('entite-clic');
}

function initialiserMaSelection(idEntite) {
    terminerMaSelection();

    jQuery('#maSelection-entite-' + idEntite).addClass('entite-clic');
}

function clickEntiteMaSelection(idEntite, fromSelecteur) {

    var $entite = recupererEntite(idEntite);
    if(!$entite.is(':visible')){
        return false;
    }

//    if ((fromSelecteur && jQuery('#bloc-ma-selection').css('width') === '0px') || (!fromSelecteur))
    if (!fromSelecteur)
        toggleMaSelection();

    if (estAValider()) {
        validerActionMessage();
        return false;
    }

    if ((jQuery('.entite-clic').length === 0) || (jQuery('.entite-clic').length === 1 && recupererEntiteIdCourante() != idEntite)) {
        if (initialiserAction(idEntite)) {
            initialiserMaSelection(idEntite);
            initialiserContenu(idEntite);
        }
    } else {
        terminerAction();
        terminerMaSelection();
        terminerContenu();
    }
    terminerRotationComposition();

    if (jQuery('#entite-photo-' + idEntite).attr('attr-composition-id') != 0) {
        initRotationComposition();
    } else {
        cacherRotationComposition();
    }
}

function updateTotalPrixSelection() {
    var total = 0;
    jQuery(".selection-entite-prix").each(function () {
        total += parseInt(jQuery(this).val());
    });
    jQuery("#total-selection span").html(total + ' €');
    if (jQuery("#budget_max").val() != 0 && jQuery("#budget_max").val() < total) {
        jQuery("#total-selection span").css('color', '#ff0000');
    } else {
        jQuery("#total-selection span").css('color', '#82C400');
    }
}

jQuery(document).ready(function () {

    //Initialiser le centrage vertical des actions
    centrerDivVerticalement('selection');

    // Le centrage des actions se fait lors des resizes
    jQuery(window).resize(function () {
        centrerDivVerticalement('selection');
        centrerDiv('fenetre-ma-selection-description');
    });

    // Initialisation du right
    jQuery('#selection').css('right', -$('#conteneur-entites').width());

    // Animation quand on clic sur l'onglet Ma sélecttion de droite
    jQuery('body').on('click', '#titre-selection', function () {
        basculerMaSelection();
    });

    // Surbrillance de l'image dans l'espace de travail quand on passe la souris dessus dans l'onglet Ma sélection
    jQuery('body').on({
        mouseenter: function () {
            var idEntite = jQuery(this).find('input.entite-id').val();
            jQuery('#entite-photo-' + idEntite).addClass('entite-surbrillance');
        },
        mouseleave: function () {
            var idEntite = jQuery(this).find('input.entite-id').val();
            jQuery('#entite-photo-' + idEntite).removeClass('entite-surbrillance');
        }
    }, '.selection-entite');

    // Clic sur une entitée
    jQuery('body').on('click', '#conteneur-entites .selection-entite div.entite-clicable', function () {
        var idEntite = jQuery(this).closest('.selection-entite').find('input.entite-id').val();
        clickEntiteMaSelection(idEntite, false);
    });

    // Clic sur les actions d'une entité
    jQuery('body').on('click', '#conteneur-entites .entite-action div', function () {
        var idEntite = jQuery(this).closest('.selection-entite').find('input.entite-id').val();

        if (jQuery(this).hasClass('entite-suppression')) {
            suppressionEntite(idEntite, false);
        } else if (jQuery(this).hasClass('entite-visibilite')) {
            visibiliteEntite(idEntite);
            jQuery('#maSelection-entite-' + idEntite + ' div.entite-image').toggleClass('entite-image-invisible');
            jQuery('#maSelection-entite-' + idEntite + ' div.entite-infos').toggleClass('entite-infos-invisible');
        }
    });

    // Clic sur la description d'une entité
    jQuery('body').on('click', '.entite-description a', function () {
        replierToutElementFixed();
        var url = jQuery(this).attr('href');

        jQuery.ajax({
            url: url
        }).done(function (html) {
            ouvrirPopinDescription(html);
        });

        return false;
    });

    //Clic sur duplication
    jQuery('body').on('click', '.entite-dupliquer a', function () {
        clicSelectionEntite(jQuery(this).attr('href'), null, null, null, null);
        return false;
    });


    jQuery('body').on('click', '#croix-fermer', function () {
        fermerPopinDescription();
    });

    // Listener les actions possibles sur une entite
    jQuery('body').on('keydown', '#filtre-formulaire', function (e) {
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
                fermerPopinDescription();
                fermerMaSelection();
                break;
        }
    });

    // Listener les actions possibles sur une entite
    jQuery('body').on('click', 'div.description-texte-photos-apercu-conteneur img', function (e) {
        jQuery('div.description-texte-photos-apercu-conteneur img').css('opacity', '0.5');
        jQuery(this).css('opacity', '1');
        jQuery('.description-texte-photo-principale img').fadeOut();
        jQuery('.description-texte-photo-principale img').attr('src', jQuery(this).attr('attr-source'));
        jQuery('div.description-texte-photo-legende').html(jQuery(this).attr('alt'));
        jQuery('.description-texte-photo-principale img').fadeIn();
    });

    //controle gauche & droite de la gallerie photo
    jQuery('body').on('click', 'div.controle-gauche', function () {
        jQuery('div.description-texte-photos-apercu').animate({
            scrollLeft: '-=50'
        }, 400, 'easeOutQuad');
    });

    jQuery('body').on('click', 'div.controle-droite', function () {
        jQuery('div.description-texte-photos-apercu').animate({
            scrollLeft: '+=50'
        }, 400, 'easeOutQuad');
    });

    jQuery('body').on('mouseenter', 'div.description-texte-photo-principale', function () {
        jQuery('div.description-texte-photo-legende').css('display', 'block');
        jQuery('body').on('mouseleave', 'div.description-texte-photo-principale', function () {
            jQuery('div.description-texte-photo-legende').css('display', 'none');
        });
    });
});