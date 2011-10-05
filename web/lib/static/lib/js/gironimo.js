if (typeof gironimo == 'undefined')
    var gironimo = {};

gironimo.log = function() {
    if (typeof console != 'undefined') console.log.apply(console, arguments);
};

gironimo.location = function(href) {
    if (typeof href == 'undefined') { return top.location.href; }
    top.location.href = href;
};

gironimo.bind = function(context, func) {
    return $.proxy(func, context);
};

Function.prototype.bind = function(context) {
    return gironimo.bind(context, this);
};

$(document).ready(function() {
    $('html').removeClass('no-js').addClass('js');
});

