jQuery(document).ready(function () {
    if (jQuery('#page-home').length !== 0) {
        resizeMain();

        jQuery(window).resize(function () {
            resizeMain();
        });

        if (jQuery('.bxslider').length > 0) {
            jQuery('.bxslider').bxSlider({
                mode: 'horizontal',
                captions: false,
                controls: false,
                autoStart: true,
                autoHover: true,
                auto: true,
                pause: 5000,
                onSlideBefore: function (slideElement, oldIndex, newIndex) {
                    var classElem = slideElement.attr('attr-id-post');

                    jQuery('.details-article').css('display', 'none');
                    jQuery('.details-article-' + classElem).css('display', 'block');
                }
            });
        }
    }
});

function resizeMain() {
    if (jQuery('#e-grounds-image-main').length > 0) {
        var widthWindow = jQuery(window).width();
        var offsetMain = jQuery('#e-grounds-image-main').offset();
        jQuery('#e-grounds-image-main').css('width', widthWindow - offsetMain.left);
    }
}