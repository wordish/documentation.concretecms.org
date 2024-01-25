<?php

defined('C5_EXECUTE') or die('Access Denied.');

/** @var \Concrete\Core\Multilingual\Service\Detector $detector */
?>

<div class="float-right" style="margin-top:-3.8rem;">
    <form method="post" class="row row-cols-auto g-0 align-items-center">
        <div class="col-auto me-3">
            <label for="switch-release-version-select"><?=t('Release Version')?></label>
        </div>
        <div class="col-auto">
            <?= $form->select(
                'release-version',
                $releaseLabels,
                $selectedRelease,
                [
                    'data-select' => 'switch-release-version',
                    'data-action' => $detector->getSwitchLink($cID, '--release--')
                ]
            ) ?>
        </div>
    </form>
    <script>
     (()=>{var e={7986:()=>{$((function(){$("select[data-select=multilingual-switch-language]").change((function(){var e=$(this).attr("data-action").replace("--language--",$(this).val());window.location.href=e}))}))}},t={};function r(a){var n=t[a];if(void 0!==n)return n.exports;var o=t[a]={exports:{}};return e[a](o,o.exports,r),o.exports}r.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return r.d(t,{a:t}),t},r.d=(e,t)=>{for(var a in t)r.o(t,a)&&!r.o(e,a)&&Object.defineProperty(e,a,{enumerable:!0,get:t[a]})},r.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),(()=>{"use strict";r(7986)})()})();
    </script>
</div>
<div class="alert alert-warning">
    <aside>
        <i class="fas fa-exclamation-triangle"></i>
        This documentation is not for the most recent release version of Concrete CMS.
    </aside>
</div>
