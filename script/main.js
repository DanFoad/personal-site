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
    svg.setAttribute("id", "test");

    var poly = document.createElementNS("http://www.w3.org/2000/svg", "polygon");
    poly.setAttribute("points", points);
    poly.setAttribute("class", "hex");
    poly.style.stroke = changeLightness("#4F6B7D", Math.floor(Math.random() * 40) - 20);

    svg.appendChild(poly);
    svg.style.left = left;
    svg.style.top = top;

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
    var top = $("#" + target).offset().top;
    $("html, body").animate({
        scrollTop: top
    }, 400);
});
