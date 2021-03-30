require('../css/global.scss');
require('../css/app.scss');

require('../../vendor/blackknight467/star-rating-bundle/blackknight467/StarRatingBundle/Resources/public/js/rating.js');
require('../../vendor/blackknight467/star-rating-bundle/blackknight467/StarRatingBundle/Resources/public/css/rating.css');

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
        const $lat = $('.location-lat');
        const $lng = $('.location-lng');
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
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
            maxZoom: 18,
        }).addTo(map);

        !readonly && map.on('click', e => {
            marker && marker.remove();
            marker = L.marker(e.latlng).addTo(map);

            $lat.val(e.latlng.lat);
            $lng.val(e.latlng.lng);
        })
    })

    const $onlineOnly = $('#session_with_detail_onlineOnly');
    $onlineOnly.click(ev => {
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

    const $fullyRemote = $('#job_with_detail_fullyRemote');
    $fullyRemote.click(ev => {
        if (ev.target.checked) {
            $('#office-job-details').hide();
            $('#office-job-details input').prop('required', false)
        } else {
            $('#office-job-details').show();
            $('#office-job-details input').prop('required', true)
        }
    });

    if ($fullyRemote.prop('checked')) {
        $('#office-job-details').hide();
        $('#office-job-details input').prop('required', false)
    }
});
