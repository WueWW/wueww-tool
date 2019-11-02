module.exports = function($) {
    $.fn.maxlengthIndicator = function() {
        this.each(function (i, el) {
            const $el = $(el);

            const maxLength = $el.attr('maxlength');
            if (maxLength === undefined) {
                return;
            }

            const $indicator = $('<span></span>')
                .addClass('maxlength-indicator')
                .appendTo($el.siblings('label'));

            function updateIndicator() {
                $indicator.text(` (${$el.val().length} von ${maxLength} Zeichen)`);
            }

            $el.on('change', updateIndicator);
            $el.on('keyup', updateIndicator);
            updateIndicator();
        });
    };
};