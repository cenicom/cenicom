$(document).ready(function () {

    $('#country_id').on('change', function () {

        let countryId = $(this).val();

        $('#state_id').empty();

        if (countryId) {

            $.get('/states/by-country/' + countryId, function (states) {

                $('#state_id').append('<option value="">Seleccione...</option>');

                states.forEach(function (state) {

                    $('#state_id').append(
                        `<option value="${state.id}">${state.name}</option>`
                    );

                });

            });

        }

    });

});
