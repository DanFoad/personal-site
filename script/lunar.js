var diameter = 200;
var blur = 10;
var phase;
var id = "moonImage";
var background = "img/moon.png"

function drawMoon(elementId, phase, removePrevious) {
    if (removePrevious) $(id).remove();

    var waxing = false;

    if (phase <= 15) {
        waxing = true;
    }
}

function calculatePhase(date) {
    var today = date || moment();
    var newMoon = moment("20150418", "YYYYMMDD");
    var diff = Math.abs(moment.duration(today.diff(newMoon)).asDays());
    diff = diff % 29.530588853; // Length of a synodic month
    return diff;
}

phase = calculatePhase();
drawMoon("moon", phase);