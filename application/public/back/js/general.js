jQuery(document).ready(function(){

    $(document).ready(function(){
        $('.translatedButtonOriginalValue').tooltipster({
            trigger: 'click',
            functionInit: function(instance, helper){
                var content = $(helper.origin).attr('data-content');
                instance.content(content);
            }
        })
    })
})