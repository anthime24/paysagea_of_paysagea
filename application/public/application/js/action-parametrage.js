function initialiserActionParametrage(actionParametrage) {
    jQuery('#action-parametrage #' + actionParametrage).css('display', 'block');
}

function terminerActionParametrage() {
    jQuery('#action-parametrage .action-parametrage-hook').css('display', 'none');
}

function changerTailleGommeCercle(taille) {
    var calculCentreLargeur = (jQuery('#taille-gomme .slider-valeur').width() / 2) - (taille / 2);
    var calculCentreHauteur = (jQuery('#taille-gomme .slider-valeur').height() / 2) - (taille / 2);
    jQuery('#taille-gomme .slider-valeur-cercle').css({
        width: taille + 'px',
        height: taille + 'px',
        top: calculCentreLargeur + 'px',
        left: calculCentreHauteur + 'px'
    });
}

jQuery(document).ready(function () {
    jQuery("#taille-gomme .slider").slider({
        min: 5,
        max: 40,
        value: recupererTailleGomme(),
        slide: function (event, ui) {
            changerTailleGomme(ui.value);
            changerTailleGommeCercle(ui.value);
        }
    });

    changerTailleGommeCercle(recupererTailleGomme());
});