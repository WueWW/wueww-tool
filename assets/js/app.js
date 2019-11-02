require('../css/global.scss');
require('../css/app.scss');

const $ = require('jquery');
require('bootstrap');
require('font-awesome/scss/font-awesome.scss');

require('./maxlength-indicator')($);

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
    $('input[maxlength], textarea[maxlength]').maxlengthIndicator();
});
