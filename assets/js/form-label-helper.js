module.exports = ($indicator, $label) => {
    const $labelSpan = $label.find('span.invalid-feedback').first();

    if ($labelSpan.length) {
        $indicator.insertBefore($labelSpan);
    } else {
        $indicator.appendTo($label);
    }
};