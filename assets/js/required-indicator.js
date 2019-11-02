module.exports = function($) {
    $.fn.requiredIndicator = function() {
        this.each(function (i, el) {
            $('<span>*</span>')
                .addClass('required-indicator')
                .attr('title', 'Pflichtfeld')
                .appendTo(el);
        });
    };
};