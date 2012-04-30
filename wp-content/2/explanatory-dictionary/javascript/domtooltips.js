/*
Name: Domtooltips
URI: http://rubensargsyan.com/domtooltips/
Author: Ruben Sargsyan
Author URI: http://rubensargsyan.com/
Version: 1.0
Created: 18.11.2009

Domtooltips by Ruben Sargsyan is licensed under a Creative Commons Attribution-Noncommercial-No Derivative Works 3.0 Unported License.
*/
function load_domtooltips(){
    all_spans = document.getElementsByTagName("span");

    if(all_spans.length>0){
        for(var i=0; i<all_spans.length; i++){
            if(all_spans[i].className=="domtooltips"){
                if(all_spans[i].parentNode.className!="domtooltips"){
                    domtooltipsspan = all_spans[i];

                    domtooltipsspan.id = "domtooltipsspan"+i;

                    domtooltipsspan.onmouseover = function(event){
                        domtooltipsdiv = document.createElement("div");
                        domtooltipsdiv.className = "domtooltips_tooltip";
                        domtooltipsdiv.innerHTML = this.title
                        this.title = "";

                        var e = event || window.event;

                        mouse_position = get_mouse_position(e);

                        domtooltipsdiv.style.left = (mouse_position.x+15) + "px";
                        domtooltipsdiv.style.top = (mouse_position.y+15) + "px";

                        document.body.appendChild(domtooltipsdiv);
                    };

                    domtooltipsspan.onmouseout = function(){
                        this.title = domtooltipsdiv.innerHTML;
                        document.body.removeChild(domtooltipsdiv);
                    };
                }else{
                    all_spans[i].title = "";
                }

            }
        }
    }
}

function get_mouse_position(e){
    return e.pageX ? {'x':e.pageX, 'y':e.pageY} : {'x':e.clientX + document.documentElement.scrollLeft + document.body.scrollLeft, 'y':e.clientY + document.documentElement.scrollTop + document.body.scrollTop};
}