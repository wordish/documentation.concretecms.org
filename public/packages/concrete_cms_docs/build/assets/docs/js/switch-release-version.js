$(function() {
    $('select[data-select=switch-release-version]').change(function() {
        var action = $(this).attr('data-action').replace('--release--', $(this).val())
        window.location.href = action
    })
})
