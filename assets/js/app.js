require('../css/global.scss');
require('../css/app.scss');

require('leaflet/dist/leaflet.css');
require('leaflet/dist/leaflet.js');

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),
    iconUrl: require('leaflet/dist/images/marker-icon.png'),
    shadowUrl: require('leaflet/dist/images/marker-shadow.png'),
});

require('bootstrap');
require('font-awesome/scss/font-awesome.scss');

require('./required-indicator')($);
require('./maxlength-indicator')($);

$(document).ready(function () {
    $('[data-toggle="popover"]').popover();
    $('input[maxlength], textarea[maxlength]').maxlengthIndicator();
    $('label.required').requiredIndicator();

    $('#map').each((index, el) => {
        const $lat = $('#session_with_detail_locationLat');
        const $lng = $('#session_with_detail_locationLng');
        const readonly = el.classList.contains('readonly');

        let marker;
        const map = L.map(el);

        if ($lat.val() !== '') {
            let latlng = [Number($lat.val()), Number($lng.val())];
            map.setView(latlng, 16);
            marker = L.marker(latlng).addTo(map);
        } else {
            map.setView([49.7924, 9.9327], 14);
        }

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution:
                'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
            maxZoom: 18,
        }).addTo(map);

        !readonly &&
            map.on('click', (e) => {
                marker && marker.remove();
                marker = L.marker(e.latlng).addTo(map);

                $lat.val(e.latlng.lat);
                $lng.val(e.latlng.lng);
            });
    });

    const $onlineOnly = $('#session_with_detail_onlineOnly');
    $onlineOnly.click((ev) => {
        if (ev.target.checked) {
            $('#offline-session-details').hide();
            $('#offline-session-details input').prop('required', false);
        } else {
            $('#offline-session-details').show();
            $('#offline-session-details input').prop('required', true);
        }
    });

    if ($onlineOnly.prop('checked')) {
        $('#offline-session-details').hide();
        $('#offline-session-details input').prop('required', false);
    }

    const $detailDate = $('#session_with_detail_date');
    const $detailStart = $('#session_with_detail_start');
    const $detailStop = $('#session_with_detail_stop');
    const $hintBox = $('#parallel-session-hint-box');
    const sessionId = location.pathname.match(/\/session\/(new|\d+)/);
    if (sessionId && $detailDate.length && $detailStart.length && $detailStop.length && $hintBox.length) {
        function updateParallelSessionStats() {
            let url =
                '/session/' +
                encodeURIComponent(sessionId[1]) +
                '/parallel/' +
                encodeURIComponent($detailDate.val()) +
                '/' +
                encodeURIComponent($detailStart.val());

            if ($detailStop.val() !== '') {
                url += '/' + encodeURIComponent($detailStop.val());
            }

            $.ajax(url).then((result) => {
                const $hint = $('<div class="alert" role="alert">');

                switch (Number.parseInt(result.count, 10)) {
                    case 0:
                        $hint
                            .addClass('alert-success')
                            .text(
                                'Sehr schön, parallel zu deinem Event findet (aktuell) keine weitere Veranstaltung statt.'
                            );
                        break;

                    case 1:
                        $hint
                            .addClass('alert-info')
                            .text('Parallel zu deinem Event findet (aktuell) eine weitere Veranstaltung statt.');
                        break;

                    case 2:
                        $hint
                            .addClass('alert-info')
                            .text('Parallel zu deinem Event finden (aktuell) zwei weitere Veranstaltungen statt.');
                        break;

                    case 3:
                        $hint
                            .addClass('alert-info')
                            .text('Parallel zu deinem Event finden (aktuell) drei weitere Veranstaltungen statt.');
                        break;

                    default:
                        $hint
                            .addClass('alert-danger')
                            .text(
                                'Zu deinem Event finden ' +
                                    result.count +
                                    ' weitere Veranstaltungen parallel statt. ' +
                                    'Bitte versuche einen alternativen Termin zu finden.'
                            )
                            .prepend('<strong>Achtung!</strong> ');
                }

                console.log('generated $hint', $hint);
                $hintBox.html('').append($hint);
            });
        }

        updateParallelSessionStats();
        $detailDate.on('change', updateParallelSessionStats);
        $detailStart.on('change', updateParallelSessionStats);
        $detailStop.on('change', updateParallelSessionStats);
    }
});
