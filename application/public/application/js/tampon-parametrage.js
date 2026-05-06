function initialiserTamponParametrage(tamponParametrage) {
    jQuery('#tampon-parametrage #' + tamponParametrage).css('display', 'block');
    centrerDivHorizontal('tampon-parametrage');
}

function terminerTamponParametrage() {
    jQuery('#tampon-parametrage .tampon-parametrage-hook').css('display', 'none');
}

function changerTailleTamponCercle(taille) {
    var calculCentreLargeur = (jQuery('#taille-tampon .slider-valeur').width() / 2) - (taille / 2);
    var calculCentreHauteur = (jQuery('#taille-tampon .slider-valeur').height() / 2) - (taille / 2);
    jQuery('#temoin-pointeur').css('width', taille);
    jQuery('#temoin-pointeur').css('height', taille);
    jQuery('#taille-tampon .slider-valeur-cercle').css({
        width: taille + 'px',
        height: taille + 'px',
        top: calculCentreLargeur + 'px',
        left: calculCentreHauteur + 'px'
    });
}

jQuery(document).ready(function () {
    jQuery("#taille-tampon .slider").slider({
        min: 5,
        max: 20,
        value: recupererTailleTampon(),
        slide: function (event, ui) {
            changerTailleTampon(ui.value);
            changerTailleTamponCercle(ui.value);
        }
    });

    changerTailleTamponCercle(recupererTailleTampon());
});

function changerTailleTampon(taille) {
    tailleTampon = taille;
}

function recupererTailleTampon() {
    return tailleTampon;
}