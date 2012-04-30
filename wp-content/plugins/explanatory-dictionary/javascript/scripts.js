jQuery(function($){
    $(document).ready(function(){
        $(".domtooltips").mouseenter(function(){
            if($(this).children(".domtooltips_tooltip").is(":animated")==false){
                $(this).children(".domtooltips_tooltip").fadeIn(300);
                var top_px = 2+parseInt($(this).css("font-size"));
                $(this).children(".domtooltips_tooltip").css("top",top_px+"px");
            }
        });

        $(".domtooltips").mouseleave(function(){
            $(this).children(".domtooltips_tooltip").fadeOut(300);
        });
    });
});