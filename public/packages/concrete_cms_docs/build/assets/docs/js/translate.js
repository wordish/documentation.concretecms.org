$(function() {
    var select = $($.parseHTML('<select class="position-fixed form-select form-select-sm d-inline-block w-auto"></select>'))
    var languages = {
        de: 'German',
        ja: 'Japanese',
        es: 'Spanish',
    }

    select.append('<option selected disabled>🌐 Translate</option>');
    for (var key of Object.keys(languages)) {
        select.append('<option value="' + key + '">' + languages[key] + '</option>');
    }

    select.change(function() {
        var url = CCM_APPLICATION_URL + encodeURIComponent(window.location.pathname)
        window.location = 'https://translate.google.com/translate?sl=auto&tl=' + $(this).val() + '&hl=en&u=' + url
    })

    select.css({right: '3rem', bottom: '1.75rem'})
    $('.ccm-docs-title').append(select)
})