var isWaitingForIntteruptionConfirm = false;

/**
 *
 * @param {type} originalEvent
 * @returns {Promise}
 *
 * Permet de définir lorsque l'on clique sur le menu
 * si l'on doit poursuivre une nouvelle action, ou continuer celle en cours
 */
function menuItemClickedEvent(originalEvent) {

    var handled = false;
    var $clickedTarget = jQuery(originalEvent.currentTarget);

    return new Promise(function (resolve, reject) {
        if (typeof (isLassoActive) != "undefined" && isLassoActive == true) {
            isWaitingForIntteruptionConfirm = true;
            handled = true;
            var erreurMsg = "<p style=\"text-align: center;\">" + translateJs('modal.interruption') + "</p>";
            var erreurMsgTitre = translateJs('modal.interruptionTitre');

            afficherErreur(erreurMsg, erreurMsgTitre, function (bConfirm) {
                if (bConfirm == "continue") {
                    isWaitingForIntteruptionConfirm = false;
                    jQuery(document).trigger('stropperLassoRequest');
                    jQuery('#popin-error').fadeOut("slow");
                    resolve(true);
                } else {
                    isWaitingForIntteruptionConfirm = false;
                    jQuery('#popin-error').fadeOut("slow");
                    resolve(false);
                }
            })
        }

        if (!handled) {
            resolve(true);
        }
    })
}