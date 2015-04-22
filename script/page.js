// GLOBAL VARIABLES
var mobile = false;
var navTop = $("header").offset().top;

/**
 * Handle responses to page scrolls
 */
$(document).scroll(function() {
    var top = $(document).scrollTop();

    $(".heading").css("background-position", "50% " + (window.pageYOffset * 0.7 - 640) + "px");

    if (mobile) { // Mobile scroll responses
    } else {

        if (top > 0) {
            $("#top").fadeIn();
        } else {
            $("#top").fadeOut();
        }

        $("header").toggleClass("header--fixed", top >= navTop);
        if (top >= navTop) {
            $(".nav__filler").show();
        } else{
            $(".nav__filler").hide();
        }

    }
});

$("#top").click(function() {
    $("html, body").animate({
        scrollTop: 0
    }, "fast");
});

// Code Highlighting
hljs.initHighlightingOnLoad();
        $(document).ready(function() {
            lineCode();
        });

        function lineCode() {
            var pre = document.getElementsByTagName('pre'),
                pl = pre.length;
            for (var i = 0; i < pl; i++) {
                pre[i].innerHTML = '<span class="line-number"></span>' + pre[i].innerHTML + '<span class="cl"></span>';
                var num = pre[i].innerHTML.split(/\n|<br>/).length;
                for (var j = 0; j < num; j++) {
                    var line_num = pre[i].getElementsByTagName('span')[0];
                    line_num.innerHTML += '<span>' + (j + 1) + '</span>';
                }
            }
        }