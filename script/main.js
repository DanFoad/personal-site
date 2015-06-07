// GLOBAL VARIABLES
var mobile = false;
var navTop = $(".nav__main").offset().top;

animateBackground();
setInterval(function() {
    animateBackground();
}, 5000);

function animateBackground() {
    $("#about").animate({
        "background-position-x" : ((Math.random() * 20) - 10) + 50 + "%",
        "background-position-y" : (Math.random() * 20) + "%"
    }, 5000);
}

/**
 * Handle responses to page scrolls
 */
$(document).scroll(function() {
    var top = $(document).scrollTop();

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

// Contact Form validation
$("#contact__name").on("keydown cut paste input", function() {
    if ($("#contact__name").val().length > 0) {
        $("#contact__name--valid").show().html("<i class='fa fa-check'></i>").addClass("valid");
        if ($("#contact__name--valid").hasClass("invalid")) $("#contact__name--valid").removeClass("invalid"); 
    } else {
        if ($("#contact__name--valid").hasClass("valid")) $("#contact__name--valid").html("<i class='fa fa-times'></i>").removeClass("valid").addClass("invalid");
    }
});

$("#contact__email").on("keydown cut paste input", function() {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if (re.test($("#contact__email").val())) {
        $("#contact__email--valid").show().html("<i class='fa fa-check'></i>").addClass("valid");
        if ($("#contact__email--valid").hasClass("invalid")) $("#contact__email--valid").removeClass("invalid"); 
    } else {
        if ($("#contact__email--valid").hasClass("valid")) $("#contact__email--valid").html("<i class='fa fa-times'></i>").removeClass("valid").addClass("invalid");
    }
});

$("#contact__form").on("submit", function(e) {
    e.preventDefault();
    $.ajax({
        url: $(this).attr("action"),
        type: 'POST',
        data: $(this).serialize(),
        beforeSend: function() {
            $("#contact__response").show().html("Sending message...");
        },
        success: function(data) {
            $("#contact__response").html(data);
        }
    });
});