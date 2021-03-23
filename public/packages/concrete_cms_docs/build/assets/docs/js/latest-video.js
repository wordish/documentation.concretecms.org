/**
 * @project:   ConcreteCMS Docs
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

$(function () {
    $('.latest-video a, .video a').magnificPopup({
        type: 'iframe',
        mainClass: 'mfp-fade',
        preloader: true
    })
});