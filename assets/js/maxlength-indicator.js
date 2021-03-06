const formLabelHelper = require('./form-label-helper');

module.exports = function($) {
    $.fn.maxlengthIndicator = function() {
        this.each(function(i, el) {
            const $el = $(el);

            const maxLength = $el.attr('maxlength');
            if (maxLength === undefined || maxLength === '255') {
                return;
            }

            const $indicator = $('<span></span>')
                .addClass('maxlength-indicator');

            formLabelHelper($indicator, $el.siblings('label'));

            function updateIndicator() {
                window.setTimeout(function() {
                    $indicator.text(` (${$el.val().length} von ${maxLength} Zeichen)`);
                }, 0);
            }

            $el.on('change', updateIndicator);
            $el.on('keyup', updateIndicator);
            $el.on('paste', updateIndicator);
            updateIndicator();
        });
    };
};
