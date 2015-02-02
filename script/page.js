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