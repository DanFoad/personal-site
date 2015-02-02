// GLOBAL VARIABLES
var mobile = false;
var navTop = $(".nav__main").offset().top;

// MAIN FUNCTIONS

// EVENT HANDLERS

/**
 * Handle responses to page scrolls
 */
$(document).scroll(function() {
    var top = $(document).scrollTop();

    $("#about").css("background-position", "50% " + window.pageYOffset * 0.7 + "px");

    if (mobile) { // Mobile scroll responses
    } else {

        if (top > 0) {
            $("#top").fadeIn();
        } else {
            $("#top").fadeOut();
        }

        $("header, #header__logo").toggleClass("header--fixed", top >= 40);
        $(".nav__main").toggleClass("nav--fixed", top >= navTop);
        if (top >= navTop) {
            $("#nav__filler").show();
        } else{
            $("#nav__filler").hide();
        }

    }
});

$("#top").click(function() {
    $("html, body").animate({
        scrollTop: 0
    }, "fast");
});

$(".button__contact").click(function() {
    $("html, body").animate({
        scrollTop: $("#contact").offset().top
    }, "fast");
});