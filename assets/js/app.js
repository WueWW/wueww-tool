/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

require('../css/global.scss');
require('../css/app.scss');

const $ = require('jquery');
require('bootstrap');
require('font-awesome/scss/font-awesome.scss');

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
});

console.log('Hello Webpack Encore!');
