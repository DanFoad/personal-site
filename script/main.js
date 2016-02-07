var mq;
if (matchMedia) {
    mq = window.matchMedia("(max-width: 480px)");
    mq.addListener(WidthChange);
} else {
    mq = {matches: true};
}

mobile = false;
function WidthChange(mq) {
    if (mq.matches) {
        if (!mobile)
            init();
        mobile = true;
    } else {
        if (mobile)
            init();
        mobile = false;
    }
}

function init() {
    $("#main-header svg polygon").each(function(i, entry) {
        $(entry).remove();
    });

    var hexLocations1 = [[40, -60, 100], [60, 110, 225], [50, 20, 155], [80, -5, 45], [120, -180, 330], [20, 60, 210], [60, 140, 100], [80, 240, 40], [30, 260, 112], [60, 330, 165], [120, 410, 110], [20, 620, 120], [60, 650, 100]];
    var hexLocations2 = [[100, 740, 40], [30, 775, 110], [80, 940, 60], [20, 910, 70], [60, 1080, 120], [120, 1140, 30], [40, 1215, 118], [80, 1325, 130], [60, 1472, 50], [20, 1491, 105], [50, 1595, 80], [30, 1650, 155], [60, 1770, 325], [70, 1630, 295], [60, 1520, 200], [150, 1700, 200]];
    addHexagons(hexLocations1);

    if (!mq.matches) {
        addHexagons(hexLocations2);
    }


    $("#main-header svg polygon").each(function(i, entry) {
        if (Math.floor(Math.random() * 4) !== 2)
            return;

        fadeHex(entry);
        setInterval(function() {
            fadeHex(entry);
        }, Math.floor(Math.random() * 4000) + 2000);
    });
}
init();

function generateHexagon(a, left, top) {
    var ri = (a / 2) * Math.sqrt(3);
    var d = 2 * a;
    var d2 = 2 * ri;
    var x = Math.sqrt(a*a - ri*ri);

    var data = {
        "a" : a,
        "ri" : ri,
        "d" : d,
        "d2" : d2,
        "x" : x
    };

    var points = "";
    points += "20," + (ri+20) + " ";
    points += (x+20) + "," + (d2+20) + " ";
    points += (d - x+20) + "," + (d2+20) + " ";
    points += (d+20) + "," + (ri+20) + " ";
    points += (x + a+20) + ",20 ";
    points += (x+20) + ",20";

    var svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
    svg.setAttribute("xmlns:xlink","http://www.w3.org/1999/xlink");
    svg.setAttribute("height", d2 + 40);
    svg.setAttribute("width", d + 40);

    var poly = document.createElementNS("http://www.w3.org/2000/svg", "polygon");
    poly.setAttribute("points", points);
    poly.setAttribute("class", "hex");
    poly.style.stroke = changeLightness("#4F6B7D", Math.floor(Math.random() * 40) - 20);

    svg.appendChild(poly);
    svg.style.left = left + "px";
    svg.style.top = top + "px";

    document.getElementById("main-header").appendChild(svg);
}

function changeLightness(color, percent) {  // deprecated. See below.
    var num = parseInt(color.slice(1),16), amt = Math.round(2.55 * percent), R = (num >> 16) + amt, G = (num >> 8 & 0x00FF) + amt, B = (num & 0x0000FF) + amt;
    return "#" + (0x1000000 + (R<255?R<1?0:R:255)*0x10000 + (G<255?G<1?0:G:255)*0x100 + (B<255?B<1?0:B:255)).toString(16).slice(1);
}

function fadeHex(el) {
    $(el).fadeOut(Math.floor(Math.random() * 600) + 400).delay(Math.floor(Math.random() * 400) + 100).fadeIn(Math.floor(Math.random() * 600) + 400);
}

function addHexagons(data) {
    for (var i = 0; i < data.length; i++) {
        generateHexagon(data[i][0], data[i][1], $("#main-header").height()-data[i][2]);
    }
}

$(".smooth-scroll").on("click", function() {
    var target = $(this).data("to");
    var top = $("#" + target).offset().top - 32;
    $("html, body").animate({
        scrollTop: top
    }, 400);
});

$(".contact__input input, #contact__message").blur(function() {
    if ($(this).val().length > 0) {
        $(this).next().addClass("contact__input--title-entered");
    } else {
        $(this).next().removeClass("contact__input--title-entered");
    }
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
        $("#contact__email--valid").show().html("<i class='fa fa-times'></i>").removeClass("valid").addClass("invalid");
    }
});

$("#contact__form").on("submit", function(e) {
    e.preventDefault();
    $.ajax({
        url: $(this).attr("action"),
        type: 'POST',
        data: $(this).serialize(),
        success: function(data) {
            $("#contact__response").html(data);
            if (data.substring(0, 5) == "Error") {
                loading.triggerFail();
            } else {
                loading.triggerSuccess();
            }
        },
    });
});

/**** CONTACT BUTTON ANIMATION ****/
/**
 * Developed by http://lmgonzalves.github.io/
 */

function LoadingButton(el, options){
    this.el = el;
    this.options = options;
    this.init();
}

LoadingButton.prototype = {
    // Initialize everything
    init: function(){
        this.infinite = true;
        this.succeed = false;
        this.initDOM();
        this.initSegments();
        this.initEvents();
    },

    // Create an span element with inner text of the button and insert the corresponding SVG beside it
    initDOM: function(){
        this.el.innerHTML = '' + this.el.innerHTML + '';
        this.span = this.el.querySelector('span');
        var div = document.createElement('div');
        div.innerHTML = document.querySelector(this.options.svg).innerHTML;
        this.svg = div.querySelector('svg');
        this.el.appendChild(this.svg);
    },

    // Initialize the segments for all the paths of the loader itself, and for the success and error animations
    initSegments: function(){
        for(var i = 0, paths = this.options.paths, len = paths.length; i < len; i++){
            paths[i].el = this.svg.querySelector(paths[i].selector);
            paths[i].begin = paths[i].begin ? paths[i].begin : 0;
            paths[i].end = paths[i].end ? paths[i].end : 0.1;
            paths[i].segment = new Segment(paths[i].el, paths[i].begin, paths[i].end);
        }
        this.success = this.el.querySelector('.success-path');
        this.error = this.el.querySelector('.error-path');
        this.error2 = this.el.querySelector('.error-path2');
        this.successSegment = new Segment(this.success, 0, 0.1);
        this.errorSegment = new Segment(this.error, 0, 0.1);
        this.errorSegment2 = new Segment(this.error2, 0, 0.1);
    },

    // Initialize the click event in loading buttons, that trigger the animation
    initEvents: function(){
        var self = this;
        self.el.addEventListener('click', function(){
            $("#contact__form").submit();
            self.el.disabled = 'disabled';
            classie.add(self.el, 'open-loading');
            self.span.innerHTML = 'Sending';
            for(var i = 0, paths = self.options.paths, len = paths.length; i < len; i++){
                paths[i].animation.call(self, paths[i].segment);
            }
        }, false);
    },

    // Make it fail
    triggerFail: function(){
        this.infinite = false;
        this.succeed = false;
    },

    // Make it succeed
    triggerSuccess: function(){
        this.infinite = false;
        this.succeed = true;
    },

    // When each animation cycle is completed, check whether any feedback has triggered and call the feedback
    // handler, otherwise it restarts again
    completed: function(reset){
        if(this.infinite){
            for(var i = 0, paths = this.options.paths, len = paths.length; i < len; i++){
                if(reset){
                    paths[i].segment.draw(0, 0.1);
                }
                paths[i].animation.call(this, paths[i].segment);
            }
        }else{
            this.handleResponse();
        }
    },

    // Handle the feedback request, and perform the success or error animation
    handleResponse: function(){
        for(var i = 0, paths = this.options.paths, len = paths.length; i < len; i++){
            paths[i].el.style.visibility = 'hidden';
        }
        if(this.succeed){
            this.success.style.visibility = 'visible';
            this.successAnimation();
        }else{
            this.error.style.visibility = 'visible';
            this.error2.style.visibility = 'visible';
            this.errorAnimation();
        }
    },

    // Success animation
    successAnimation: function(){
        var self = this;
        self.successSegment.draw('100% - 50', '100%', 0.4, {callback: function(){
            self.span.innerHTML = 'Succeed';
            classie.add(self.el, 'succeed');
            //setTimeout(function(){ self.reset(); }, 2000);
        }});
    },

    // Error animation
    errorAnimation: function(){
        var self = this;
        self.errorSegment.draw('100% - 42.5', '100%', 0.4);
        self.errorSegment2.draw('100% - 42.5', '100%', 0.4, {callback: function(){
            self.span.innerHTML = 'Failed';
            classie.add(self.el, 'failed');
            setTimeout(function(){ self.reset(); }, 2000);
        }});
    },

    // Reset the entire loading button to the initial state
    reset: function(){
        this.el.removeAttribute('disabled');
        classie.remove(this.el, 'open-loading');
        this.span.innerHTML = 'Send';
        classie.remove(this.el, 'succeed');
        classie.remove(this.el, 'failed');
        this.resetSegments();
        this.infinite = true;
        for(var i = 0, paths = this.options.paths, len = paths.length; i < len; i++){
            paths[i].el.style.visibility = 'visible';
        }
        this.success.style.visibility = 'hidden';
        this.error.style.visibility = 'hidden';
        this.error2.style.visibility = 'hidden';
    },

    // Reset the segments to the initial state
    resetSegments: function(){
        for(var i = 0, paths = this.options.paths, len = paths.length; i < len; i++){
            paths[i].segment.draw(paths[i].begin, paths[i].end);
        }
        this.successSegment.draw(0, 0.1);
        this.errorSegment.draw(0, 0.1);
        this.errorSegment2.draw(0, 0.1);
    }
};

function circularLoading(){
    var button = document.querySelector('.button__contact'),
        options = {
            svg: '#circular-loading',
            paths: [
                {selector: '.outer-path', animation: outerAnimation},
                {selector: '.inner-path', animation: innerAnimation}
            ]
        },
        loading = new LoadingButton(button, options);

    function outerAnimation(segment){
        var self = this;
        segment.draw('15%', '25%', 0.2, {callback: function(){
            segment.draw('75%', '150%', 0.3, {circular:true, callback: function(){
                segment.draw('70%', '75%', 0.3, {circular:true, callback: function(){
                    segment.draw('100%', '100% + 0.1', 0.4, {circular:true, callback: function(){
                        self.completed(true);
                    }});
                }});
            }});
        }});
    }

    function innerAnimation(segment){
        segment.draw('20%', '80%', 0.6, {callback: function(){
            segment.draw('100%', '100% + 0.1', 0.6, {circular:true});
        }});
    }

    return loading;
}

/**
 * segment - A little JavaScript class (without dependencies) to draw and animate SVG path strokes
 * @version v1.0
 * @link https://github.com/lmgonzalves/segment
 * @license MIT
 */

(function(){
    var lastTime = 0;
    var vendors = ['ms', 'moz', 'webkit', 'o'];
    for(var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x){
        window.requestAnimationFrame = window[vendors[x]+'RequestAnimationFrame'];
        window.cancelAnimationFrame = window[vendors[x]+'CancelAnimationFrame']
                                   || window[vendors[x]+'CancelRequestAnimationFrame'];
    }

    if(!window.requestAnimationFrame)
        window.requestAnimationFrame = function(callback, element){
            var currTime = new Date().getTime();
            var timeToCall = Math.max(0, 16 - (currTime - lastTime));
            var id = window.setTimeout(function(){ callback(currTime + timeToCall); },
              timeToCall);
            lastTime = currTime + timeToCall;
            return id;
        };

    if(!window.cancelAnimationFrame)
        window.cancelAnimationFrame = function(id){
            clearTimeout(id);
        };
}());

function Segment(path, begin, end){
    this.path = path;
    this.length = path.getTotalLength();
    this.path.style.strokeDashoffset = this.length * 2;
    this.begin = typeof begin !== 'undefined' ? this.valueOf(begin) : 0;
    this.end = typeof end !== 'undefined' ? this.valueOf(end) : this.length;
    this.circular = false;
    this.timer = null;
    this.draw(this.begin, this.end);
}

Segment.prototype = {
    draw : function(begin, end, duration, options){
        if(duration){
            var delay = options && options.hasOwnProperty('delay') ? parseFloat(options.delay) * 1000 : 0,
                easing = options && options.hasOwnProperty('easing') ? options.easing : null,
                callback = options && options.hasOwnProperty('callback') ? options.callback : null,
                that = this;

            this.circular = options && options.hasOwnProperty('circular') ? options.circular : false;

            this.stop();
            if(delay){
                delete options.delay;
                this.timer = setTimeout(function(){
                    that.draw(begin, end, duration, options);
                }, delay);
                return this.timer;
            }

            var startTime = new Date(),
                initBegin = this.begin,
                initEnd = this.end,
                finalBegin = this.valueOf(begin),
                finalEnd = this.valueOf(end);

            (function calc(){
                var now = new Date(),
                    elapsed = (now-startTime)/1000,
                    time = (elapsed/parseFloat(duration)),
                    t = time;

                if(typeof easing === 'function'){
                    t = easing(t);
                }

                if(time > 1){
                    t = 1;
                }else{
                    that.timer = window.requestAnimationFrame(calc);
                }

                that.begin = initBegin + (finalBegin - initBegin) * t;
                that.end = initEnd + (finalEnd - initEnd) * t;

                that.begin = that.begin < 0 && !that.circular ? 0 : that.begin;
                that.begin = that.begin > that.length && !that.circular ? that.length : that.begin;
                that.end = that.end < 0 && !that.circular ? 0 : that.end;
                that.end = that.end > that.length && !that.circular ? that.length : that.end;

                if(that.end - that.begin < that.length && that.end - that.begin > 0){
                    that.draw(that.begin, that.end);
                }else{
                    if(that.circular && that.end - that.begin > that.length){
                        that.draw(0, that.length);
                    }else{
                        that.draw(that.begin + (that.end - that.begin), that.end - (that.end - that.begin));
                    }
                }

                if(time > 1 && typeof callback === 'function'){
                    return callback.call(that);
                }
            })();
        }else{
            this.path.style.strokeDasharray = this.strokeDasharray(begin, end);
        }
    },

    strokeDasharray : function(begin, end){
        this.begin = this.valueOf(begin);
        this.end = this.valueOf(end);
        if(this.circular){
            var division = this.begin > this.end || (this.begin < 0 && this.begin < this.length * -1)
                ? parseInt(this.begin / parseInt(this.length)) : parseInt(this.end / parseInt(this.length));
            if(division !== 0){
                this.begin = this.begin - this.length * division;
                this.end = this.end - this.length * division;
            }
        }
        if(this.end > this.length){
            var plus = this.end - this.length;
            return [this.length, this.length, plus, this.begin - plus, this.end - this.begin].join(' ');
        }
        if(this.begin < 0){
            var minus = this.length + this.begin;
            if(this.end < 0){
                return [this.length, this.length + this.begin, this.end - this.begin, minus - this.end, this.end - this.begin, this.length].join(' ');
            }else{
                return [this.length, this.length + this.begin, this.end - this.begin, minus - this.end, this.length].join(' ');
            }
        }
        return [this.length, this.length + this.begin, this.end - this.begin].join(' ');
    },

    valueOf: function(input){
        var val = parseFloat(input);
        if(typeof input === 'string' || input instanceof String){
            if(~input.indexOf('%')){
                var arr;
                if(~input.indexOf('+')){
                    arr = input.split('+');
                    val = this.percent(arr[0]) + parseFloat(arr[1]);
                }else if(~input.indexOf('-')){
                    arr = input.split('-');
                    val = arr[0] ? this.percent(arr[0]) - parseFloat(arr[1]) : -this.percent(arr[1]);
                }else{
                    val = this.percent(input);
                }
            }
        }
        return val;
    },

    stop : function(){
        window.cancelAnimationFrame(this.timer);
        this.timer = null;
    },

    percent : function(value){
        return parseFloat(value) / 100 * this.length;
    }
};

/*!
 * classie - class helper functions
 * from bonzo https://github.com/ded/bonzo
 *
 * classie.has( elem, 'my-class' ) -> true/false
 * classie.add( elem, 'my-new-class' )
 * classie.remove( elem, 'my-unwanted-class' )
 * classie.toggle( elem, 'my-class' )
 */

/*jshint browser: true, strict: true, undef: true */
/*global define: false */

( function( window ) {

'use strict';

// class helper functions from bonzo https://github.com/ded/bonzo

function classReg( className ) {
  return new RegExp("(^|\\s+)" + className + "(\\s+|$)");
}

// classList support for class management
// altho to be fair, the api sucks because it won't accept multiple classes at once
var hasClass, addClass, removeClass;

if ( 'classList' in document.documentElement ) {
  hasClass = function( elem, c ) {
    return elem.classList.contains( c );
  };
  addClass = function( elem, c ) {
    elem.classList.add( c );
  };
  removeClass = function( elem, c ) {
    elem.classList.remove( c );
  };
}
else {
  hasClass = function( elem, c ) {
    return classReg( c ).test( elem.className );
  };
  addClass = function( elem, c ) {
    if ( !hasClass( elem, c ) ) {
      elem.className = elem.className + ' ' + c;
    }
  };
  removeClass = function( elem, c ) {
    elem.className = elem.className.replace( classReg( c ), ' ' );
  };
}

function toggleClass( elem, c ) {
  var fn = hasClass( elem, c ) ? removeClass : addClass;
  fn( elem, c );
}

var classie = {
  // full names
  hasClass: hasClass,
  addClass: addClass,
  removeClass: removeClass,
  toggleClass: toggleClass,
  // short names
  has: hasClass,
  add: addClass,
  remove: removeClass,
  toggle: toggleClass
};

// transport
if ( typeof define === 'function' && define.amd ) {
  // AMD
  define( classie );
} else {
  // browser global
  window.classie = classie;
}

})( window );

var loading = circularLoading();
