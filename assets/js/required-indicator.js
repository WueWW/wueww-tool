const formLabelHelper = require('./form-label-helper');

module.exports = function($) {
    $.fn.requiredIndicator = function() {
        this.each(function(i, el) {
            const $indicator = $('<span>*</span>')
                .addClass('required-indicator')
                .attr('title', 'Pflichtfeld');

            formLabelHelper($indicator, $(el));
        });
    };
};
