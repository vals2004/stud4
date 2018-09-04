// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
var $ = require('jquery');

require('bootstrap');

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
});

