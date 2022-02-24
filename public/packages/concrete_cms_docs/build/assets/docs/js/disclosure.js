const hasCookie = () => /ccm_cdd=1/.test(document.cookie)
const setCookie = cookie => document.cookie = cookie
window.addEventListener('load', () => {
    // Early return if the disclosure has already been acknowledged
    if (hasCookie()) {
        return
    }

    // Render and append elements to the dom
    const templateElement = $('script.disclosure[role=template]')

    const template = _.template(templateElement.length > 0 ? templateElement.text().trim() : '')
    const disclosure = $($.parseHTML(template()))

    if (disclosure.length) {
        disclosure.appendTo(document.body).addClass('open')

        // Handle ack press
        $('button.ack').on('click', async () => {
            disclosure.addClass('close').removeClass('open')
            // Note this 500 matches the timing in `_disclosure.scss` for the `fadeout` animation
            setTimeout(() => disclosure.remove(), 500)

            // Set a 100 year cookie
            const sld = window.location.host.split('.').slice(-2).join('.')
            setCookie('ccm_cdd=1;path=/;samesite=lax;domain=.' + sld + ';max-age=' + (31536000 * 100))
        })
    }
})
